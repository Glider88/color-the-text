<?php declare(strict_types=1);

namespace App\Contract\LLM;

use App\LLM\LlmResponse;

interface OpenLikeApiInterface
{
    /** @return array<string> */
    public function models(): array;

    /** @return iterable<LlmResponse> */
    public function send(string $content, string $model): iterable;
}
