<?php declare(strict_types=1);

namespace App\LLM;

use App\Contract\LLM\OpenLikeApiInterface;
use App\Helper\IteratorHelper;
use Illuminate\Http\Client\Factory;
use LlmConfig;
use Psr\Log\LoggerInterface;

readonly class OpenLikeApi implements OpenLikeApiInterface
{
    public function __construct(
        private Factory $httpClient,
        private LlmConfig $llmConfig,
        private LoggerInterface $logger,
    ) {}

    /** @return iterable<LlmResponse> */
    public function send(string $content): iterable
    {
        $message = [
            [
                'role' => 'user',
                'content' => $content,
            ],
        ];

        $response = $this
            ->httpClient
            ->timeout($this->llmConfig->responseTimeoutSeconds)
            ->withOptions(['stream' => true])
            ->post(
                $this->llmConfig->url,
                [
                    "model" => $this->llmConfig->model,
                    "messages" => json_encode($message, JSON_THROW_ON_ERROR),
                    "temperature" => $this->llmConfig->temperature,
                    "max_tokens" => -1,
                    "stream" => true,
                ]
        );

        $this->logger->debug('request to llm sent');

        $body = $response->toPsrResponse()->getBody();

        $streamIter = IteratorHelper::fromStream($body);
        $llmLines = IteratorHelper::lines($streamIter);
        $llmPieces = IteratorHelper::map($llmLines, LlmResponse::new(...));

        return $llmPieces;
    }
}
