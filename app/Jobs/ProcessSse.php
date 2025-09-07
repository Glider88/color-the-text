<?php declare(strict_types=1);

namespace App\Jobs;

use App\Contract\SSE\MercureHubInterface;
use App\Models\Article;
use App\SSE\MercureHub;
use Illuminate\Container\Attributes\Give;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Redis\RedisManager;
use MercureConfig;
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
        LoggerInterface $logger,
        RedisManager $redis,
        MercureConfig $mercureConfig,
    ): void
    {
        $logger->debug('sse job start');

        $article = Article::find($this->articleId);
        $hasTopic = $redis->exists($mercureConfig->topicPrefix . $this->articleId);
        if (! $hasTopic) {
            ProcessLlm::dispatch($article->id);
        }

        $currentResult = 0;
        while (true) {
            /** @var $results array<string> */
            $results = $redis->lrange(
                $mercureConfig->topicPrefix . $this->articleId,
                start: $currentResult,
                end: -1
            );

            foreach ($results as $result) {
                $logger->debug('sse result: ' . $result);
                $hub->publish($this->articleId, $result);
                $currentResult += 1;

                if ($result === 'finish') {
                    $logger->debug('sse job end');

                    return;
                }
            }
        }
    }
}
