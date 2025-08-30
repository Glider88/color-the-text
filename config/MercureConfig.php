<?php declare(strict_types=1);

use Illuminate\Container\Attributes\Singleton;

#[Singleton]
readonly class MercureConfig
{
    public string $url;
    public string $topic;
    public string $jwtSecret;

    public function __construct() {
        $this->url = env('MERCURE_URL', 'http://127.0.0.1:8000/.well-known/mercure');
        $this->topic = env('MERCURE_TOPIC', 'color-the-text');
        $this->jwtSecret = env('MERCURE_JWT_SECRET', '');
    }
}
