<?php declare(strict_types=1);

namespace App\Contract\LLM;

use App\LLM\LlmResponse;

interface OpenLikeApiInterface
{
    /** @return list<string> */
    public function models(): array;

    /** @return iterable<LlmResponse> */
    public function send(string $content, string $model): iterable;
}
