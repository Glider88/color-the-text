<?php declare(strict_types=1);

namespace App\Jobs;

use App\Contract\LLM\LlmResultStorageInterface;
use App\Contract\LLM\OpenLikeApiInterface;
use App\Contract\LLM\TaskInterface;
use App\Helper\IteratorHelper;
use App\LLM\LlmResultStorageRedis;
use App\LLM\LlmResponse;
use App\LLM\OpenLikeApi;
use App\LLM\SubjectPredicateTask;
use App\Models\Article;
use Illuminate\Container\Attributes\Give;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
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
        #[Give(OpenLikeApi::class)] OpenLikeApiInterface                $api,
        #[Give(SubjectPredicateTask::class)] TaskInterface              $llmTask,
        #[Give(LlmResultStorageRedis::class)] LlmResultStorageInterface $storage,
        LoggerInterface                                                 $logger,
    ): void
    {
        $logger->debug('llm job start');
        $article = Article::find($this->articleId);

        $content = $llmTask->appendToText($article->content);
        $response = $api->send($content, $article->model);

        $storage->start($article);

        $llmPieces = IteratorHelper::map($response, static fn(LlmResponse $r) => $r->content());
        $words = IteratorHelper::lines($llmPieces);
        $pureWords = IteratorHelper::skipThinking($words);

        foreach ($pureWords as $word) {
            $wordAndType = $llmTask->processResponse($word);
            $storage->put($article, $wordAndType);
        }

        $storage->finish($article);
        $logger->debug('llm job end');
    }
}
