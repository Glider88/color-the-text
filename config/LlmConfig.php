<?php declare(strict_types=1);

use Illuminate\Container\Attributes\Singleton;

#[Singleton]
readonly class LlmConfig
{
    public string $modelsUrl;
    public string $chatUrl;
    public string $model;
    public float $temperature;
    public int $responseTimeoutSeconds;

    public function __construct() {
        $host = env('LLM_URL_HOST', 'http://host.docker.internal:1234/');
        $models = env('LLM_PATH_MODELS', '/v1/models');
        $chat = env('LLM_PATH_CHAT', '/v1/chat/completions');

        $this->modelsUrl = Uri::of($host)->withPath($models)->value();
        $this->chatUrl = Uri::of($host)->withPath($chat)->value();

        $this->temperature = (float) env('LLM_TEMPERATURE', '0.7');
        $this->responseTimeoutSeconds = (int) env('LLM_RESPONSE_TIMEOUT_SECONDS', 600);
    }
}
