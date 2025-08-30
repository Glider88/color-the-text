<?php declare(strict_types=1);

use Illuminate\Container\Attributes\Singleton;

#[Singleton]
readonly class LlmConfig
{
    public string $url;
    public string $model;
    public float $temperature;
    public int $responseTimeoutSeconds;

    public function __construct() {
        $this->url = env('LLM_URL', 'http://host.docker.internal:1234/v1/chat/completions');
        $this->model = env('LLM_MODEL', '');
        $this->temperature = (float) env('LLM_TEMPERATURE', '0.7');
        $this->responseTimeoutSeconds = (int) env('LLM_RESPONSE_TIMEOUT_SECONDS', 600);
    }
}


