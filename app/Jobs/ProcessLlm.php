<?php declare(strict_types=1);

namespace App\Jobs;

use App\Contract\LLM\OpenLikeApiInterface;
use App\Contract\LLM\TaskInterface;
use App\Helper\IteratorHelper;
use App\LLM\LlmResponse;
use App\LLM\OpenLikeApi;
use App\LLM\SubjectPredicateTask;
use App\Models\Article;
use Illuminate\Container\Attributes\Give;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Redis\RedisManager;
use MercureConfig;
use Psr\Log\LoggerInterface;

class ProcessLlm implements ShouldQueue
{
    use Queueable;

    public int $timeout = 3600;

    public function __construct(
        private readonly int $articleId,
    ) {
        $this->queue = 'llm';
    }

    public function handle(
        #[Give(OpenLikeApi::class)] OpenLikeApiInterface $api,
        #[Give(SubjectPredicateTask::class)] TaskInterface $llmTask,
        LoggerInterface $logger,
        RedisManager $redis,
        MercureConfig $mercureConfig,
    ): void
    {
        $logger->debug('llm job start');
        $article = Article::find($this->articleId);

        $content = $llmTask->appendToText($article->content);
        $response = $api->send($content, $article->model);

        $redis->rpush($mercureConfig->topicPrefix . $this->articleId, 'start');

        $llmPieces = IteratorHelper::map($response, static fn(LlmResponse $r) => $r->content());
        $words = IteratorHelper::lines($llmPieces);
        $fixedWords = $this->skipThinking($words);

        foreach ($fixedWords as $word) {
            $result = json_encode(
                $llmTask->processResponse($word),
                JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR
            );
            $logger->debug('result: ' . $result);
            $redis->rpush($mercureConfig->topicPrefix . $this->articleId, $result);
        }

        $redis->rpush($mercureConfig->topicPrefix . $this->articleId, 'finish');
        $logger->debug('llm job end');
    }

    private function skipThinking(iterable $iterator): iterable
    {
        $skip = false;
        foreach ($iterator as $value) {
            if ($value === '<think>') {
                $skip = true;
                continue;
            }

            if ($value === '</think>') {
                $skip = false;
                continue;
            }

            if ($skip) {
                continue;
            }

            yield $value;
        }
    }
}
