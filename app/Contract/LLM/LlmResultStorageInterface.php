<?php declare(strict_types=1);

namespace App\Contract\LLM;

use App\Models\Article;

interface LlmResultStorageInterface
{
    public const string START = 'start';
    public const string FINISH = 'finish';

    public function storageNotExist(Article $article): bool;
    public function start(Article $article): void;
    public function finish(Article $article): void;
    public function put(Article $article, mixed $value): void;

    /** @return array<string> */
    public function fetch(Article $article, int $offset = 0): array;
    public function deleteStorage(Article $article): void;
}
