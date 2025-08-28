<?php declare(strict_types=1);

namespace App\LLM;

enum PartOfSpeechEnum: int
{
    case UNKNOWN = 0;
    case SUBJECT = 1;
    case PREDICATE = 2;

    public static function fromNumbers(int $number): PartOfSpeechEnum
    {
        return
            match ($number) {
                1 => self::SUBJECT,
                2 => self::PREDICATE,
                default => self::UNKNOWN,
            };
    }
}
