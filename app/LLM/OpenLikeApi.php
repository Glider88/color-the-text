<?php declare(strict_types=1);

namespace App\LLM;

use App\Contract\LLM\OpenLikeApiInterface;
use App\Helper\IteratorHelper;
use Illuminate\Http\Client\Factory;
use Config\LlmConfig;
use Illuminate\Http\Client\Response;
use Psr\Log\LoggerInterface;

readonly class OpenLikeApi implements OpenLikeApiInterface
{
    public function __construct(
        private Factory $httpClient,
        private LlmConfig $llmConfig,
        private LoggerInterface $logger,
    ) {}

    /** @inheritDoc */
    public function models(): array
    {
        /** @var Response $response */
        $response = $this->httpClient->get($this->llmConfig->modelsUrl);

        /** @var array<string, array<string, string>> $data */
        $data = json_decode($response->body(), associative: true, flags: JSON_THROW_ON_ERROR);
        $models = array_column($data['data'] ?? [], 'id');

        return $models;
    }

    /** @return iterable<LlmResponse> */
    public function send(string $content, string $model): iterable
    {
        $message = [
            [
                'role' => 'user',
                'content' => $content,
            ],
        ];

        /** @var Response $response */
        $response = $this
            ->httpClient
            ->timeout($this->llmConfig->responseTimeoutSeconds)
            ->withOptions(['stream' => true])
            ->post(
                $this->llmConfig->chatUrl,
                [
                    "model" => $model,
                    "messages" => json_encode($message, flags: JSON_THROW_ON_ERROR),
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
