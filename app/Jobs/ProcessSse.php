<?php declare(strict_types=1);

namespace App\Jobs;

use App\Contract\LLM\LlmResultStorageInterface;
use App\Contract\SSE\MercureHubInterface;
use App\LLM\LlmResultStorageRedis;
use App\Models\Article;
use App\SSE\MercureHub;
use Illuminate\Container\Attributes\Give;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Psr\Log\LoggerInterface;

class ProcessSse implements ShouldQueue
{
    use Queueable;

    public int $timeout = 3600;

    public function __construct(
        private readonly int $articleId,
    ) {
        $this->queue = 'sse';
    }

    public function handle(
        #[Give(MercureHub::class)]  MercureHubInterface $hub,
        #[Give(LlmResultStorageRedis::class)] LlmResultStorageInterface $storage,
        LoggerInterface $logger,
    ): void
    {
        $logger->debug('sse job start');

        $article = Article::find($this->articleId);
        if ($storage->storageNotExist($article)) {
            ProcessLlm::dispatch($article->id);
        }

        $currentOffset = 0;
        while (true) {
            $results = $storage->fetch($article, $currentOffset);
            foreach ($results as $result) {
                $logger->debug('sse result: ' . $result);
                $hub->publish($this->articleId, $result);
                $currentOffset += 1;

                if ($result === LlmResultStorageInterface::FINISH) {
                    $logger->debug('sse job end');

                    return;
                }
            }
        }
    }
}
