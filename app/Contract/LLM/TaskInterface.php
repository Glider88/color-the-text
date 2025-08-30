<?php declare(strict_types=1);

namespace App\Contract\LLM;

interface TaskInterface
{
    public function appendToText(string $text): string;

    public function processResponse(string $word): ?array;
}
