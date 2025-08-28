<?php declare(strict_types=1);

namespace App\Jobs;

use App\Helper\IteratorHelper;
use App\LLM\LlmResponse;
use App\LLM\PartOfSpeechEnum;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Symfony\Component\Mercure\Hub;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Jwt\StaticTokenProvider;
use Symfony\Component\Mercure\Update;

class ProcessLlm implements ShouldQueue
{
    use Queueable;

    public int $timeout = 600;

    private string $text;

    public function __construct(string $text)
    {
        $this->text = $text;
    }

    public function handle(): void
    {
        $this->llm();
    }

    private function llm()
    {
        Log::debug('llm start');
        $hub = $this->hub();

        $content = <<<TEXT
Пройдись по всем предложениям от первого до последнего и найди все подлежащие и скажуемые для каждого предложения. Формат ответа: ничего лишнего, сначала метка потом пробел и само слово, метка для полежащеко 1, для сказуемого 2, каждая метка + слово на новой строчке. Вот текст:

$this->text
TEXT;

        $messages = [
            [
                'role' => 'user',
                'content' => $content,
            ],
        ];

        $response = Http
            ::timeout(600)
            ->withOptions(['stream' => true])
            ->post('http://host.docker.internal:1234/v1/chat/completions',
                [
                    "model" => "google/gemma-3-12b",
                    "messages" => json_encode($messages, JSON_THROW_ON_ERROR),
                    "temperature" => 0.7,
                    "max_tokens" => -1,
                    "stream" => true,
                ]
            );

        Log::debug('request to llm sent');

        $body = $response->toPsrResponse()->getBody();

        $streamIter = IteratorHelper::fromStream($body, 32);
        $llmLines = IteratorHelper::lines($streamIter);
        $llmPieces = IteratorHelper::map(
            $llmLines,
            static fn(string $line) => LlmResponse::new($line)->content()
        );
        $words = IteratorHelper::lines($llmPieces);

        foreach ($words as $word) {
            Log::debug('WORD: ' . $word);
            $isOk = preg_match('/^(\d+)\s+(.*)$/u', $word, $matches);
            if (! $isOk) {
                continue;
            }
            $result = json_encode(
                [
                    'word' => $matches[2],
                    'type' => PartOfSpeechEnum::fromNumbers((int) $matches[1])->value,
                ],
                JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR
            );
            Log::debug('result: ' . $result);
            $update = new Update('color-the-text', $result);
            $hub->publish($update);
        }
    }

    private function jwtToken(): string
    {
        $jwtConfiguration = Configuration::forSymmetricSigner(
            new Sha256(),
            InMemory::plainText(config('octane.mercure.publisher_jwt'))
        );

        $jwtToken = $jwtConfiguration
            ->builder()
            ->withClaim('mercure', ['publish' => ['*']])
            ->getToken($jwtConfiguration->signer(), $jwtConfiguration->signingKey())
            ->toString();

        return $jwtToken;
    }

    private function hub(): HubInterface
    {
        $jwtToken = $this->jwtToken();

        return new Hub("http://127.0.0.1:8000/.well-known/mercure", new StaticTokenProvider($jwtToken));
    }
}
