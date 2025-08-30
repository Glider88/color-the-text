<?php declare(strict_types=1);

namespace App\Contract\SSE;

interface JwtTokenInterface
{
    public function token(): string;
}
