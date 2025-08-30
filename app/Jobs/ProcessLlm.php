<?php declare(strict_types=1);

namespace App\Jobs;

use App\Contract\LLM\OpenLikeApiInterface;
use App\Contract\LLM\TaskInterface;
use App\Contract\SSE\MercureHubInterface;
use App\Helper\IteratorHelper;
use App\LLM\LlmResponse;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Psr\Log\LoggerInterface;

class ProcessLlm implements ShouldQueue
{
    use Queueable;

    public int $timeout = 600;

    public function __construct(
        private readonly string     $text,
    ) {}

    public function handle(
        MercureHubInterface $hub,
        OpenLikeApiInterface $api,
        TaskInterface $llmTask,
        LoggerInterface $logger,
    ): void
    {
        $logger->debug('llm job start');
        $content = $llmTask->appendToText($this->text);
        $response = $api->send($content);

        $llmPieces = IteratorHelper::map($response, static fn(LlmResponse $r) => $r->content());
        $words = IteratorHelper::lines($llmPieces);

        foreach ($words as $word) {
            $result = json_encode(
                $llmTask->processResponse($word),
                JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR
            );
            $logger->debug('result: ' . $result);
            $hub->publish($result);
        }
    }
}
