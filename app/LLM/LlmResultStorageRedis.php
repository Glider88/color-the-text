<?php declare(strict_types=1);

namespace App\LLM;

use App\Contract\LLM\LlmResultStorageInterface;
use App\Models\Article;
use Illuminate\Redis\RedisManager;
use Config\MercureConfig;
use Psr\Log\LoggerInterface;

readonly class LlmResultStorageRedis implements LlmResultStorageInterface
{
    public function __construct(
        private RedisManager $redis,
        private MercureConfig $mercureConfig,
        private LoggerInterface $logger,
    ) {}

    public function start(Article $article): void
    {
        $this->redis->rpush($this->key($article), self::START);
        $this->logger->debug("storage started for Article#$article->id");
    }

    public function put(Article $article, mixed $value): void
    {
        $result = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
        $this->redis->rpush($this->key($article), $result);
        $this->logger->debug("putted in storage for Article#$article->id: $result");
    }

    /** @return list<string> */
    public function fetch(Article $article, int $offset = 0): array
    {
        /** @var list<string> $results */
        $results = $this->redis->lrange($this->key($article), start: $offset, end: -1);

        return $results;
    }

    public function finish(Article $article): void
    {
        $this->redis->rpush($this->key($article), self::FINISH);
        $this->logger->debug("finished for Article#$article->id");
    }

    public function deleteStorage(Article $article): void
    {
        $this->redis->del($this->key($article));
        $this->logger->debug("storage deleted for Article#$article->id");
    }

    public function storageNotExist(Article $article): bool
    {
        return ! $this->redis->exists($this->key($article));
    }

    private function key(Article $article): string
    {
        /** @var int|null $id */
        $id = $article->id;
        if ($id === null) {
            throw new \InvalidArgumentException("Article '$article->title' id is null");
        }

        return $this->mercureConfig->topicPrefix . $article->id;
    }
}
