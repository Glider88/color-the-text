<?php declare(strict_types=1);

namespace App\Contract\LLM;

use App\LLM\LlmResponse;

interface OpenLikeApiInterface
{
    /** @return iterable<LlmResponse> */
    public function send(string $content): iterable;
}
