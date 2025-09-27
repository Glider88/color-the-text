<?php declare(strict_types=1);

namespace Config;

use Illuminate\Container\Attributes\Singleton;

#[Singleton]
readonly class MercureConfig
{
    public string $url;
    public string $topicPrefix;
    public string $jwtSecret;

    public function __construct() {
        $this->url = env('MERCURE_URL', 'http://127.0.0.1:8000/.well-known/mercure');
        $this->topicPrefix = env('MERCURE_TOPIC_PREFIX', 'color-the-text-');
        $this->jwtSecret = env('MERCURE_JWT_SECRET', '');
    }
}
