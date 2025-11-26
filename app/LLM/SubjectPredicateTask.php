<?php declare(strict_types=1);

namespace App\LLM;

use App\Contract\LLM\TaskInterface;

readonly class SubjectPredicateTask implements TaskInterface
{
    public function appendToText(string $text): string
    {
        $task = <<<TASK
Пройдись по всем предложениям от первого до последнего и найди все подлежащие и сказуемые для каждого предложения. Формат ответа: ничего лишнего, сначала метка потом пробел и само слово, метка для полежащего 1, для сказуемого 2, каждая метка + слово на новой строчке. Вот текст:

TASK;
        return $task . $text;
    }

    public function processResponse(string $word): ?array
    {
        $isOk = preg_match('/^(\d+)\s+(.*)$/u', $word, $matches);
        if (! $isOk) {
            return null;
        }

        return [
            'word' => trim($matches[2]),
            'type' => PartOfSpeechEnum::fromNumbers((int) $matches[1])->value,
        ];
    }
}
