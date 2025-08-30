<?php declare(strict_types=1);

namespace App\Contract\SSE;

interface MercureHubInterface
{
    public function publish(string $data): void;
}
