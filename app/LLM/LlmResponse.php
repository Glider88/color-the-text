<?php declare(strict_types=1);

namespace App\LLM;

readonly class LlmResponse
{
    private function __construct(
        private array $response
    ) {}

    public static function new(string $llmResponseLine): self
    {
        $emptyResult = new self([]);
        if (empty($llmResponseLine)) {
            return $emptyResult;
        }

        $start = strpos($llmResponseLine, "{");
        $end = strrpos($llmResponseLine, "}");
        if ($start === false || $end === false) {
            return $emptyResult;
        }

        $llmResponseLine = substr($llmResponseLine, $start, $end - $start + 1);
        $response = json_decode($llmResponseLine, associative: true, flags: JSON_THROW_ON_ERROR);


        return new self($response);

    }

    public function content(): string
    {
        return $this->response['choices'][0]['delta']['content'] ?? '';
    }
}
