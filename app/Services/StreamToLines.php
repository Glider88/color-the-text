<?php declare(strict_types=1);

namespace App\Services;

use Generator;
use Psr\Http\Message\StreamInterface;

readonly class StreamToLines
{
    private const int DEFAULT_CHUNK_SIZE = 8192;

    public function __construct(
        private StreamInterface $stream,
        private int $chunkSize = self::DEFAULT_CHUNK_SIZE,
    ) {}

    /** @return Generator<string> */
    public function lines(): Generator
    {
        foreach ($this->line() as $line) {
            yield $line;
        }
    }

    /** @return Generator<string> */
    private function line(): Generator
    {
        $buffer = '';

        while (!$this->stream->eof()) {
            $buffer .= $this->stream->read($this->chunkSize);

            while (($pos = strpos($buffer, "\n")) !== false) {
                $line = substr($buffer, 0, $pos);
                $buffer = substr($buffer, $pos + 1);
                yield $line;
            }
        }

        if ($buffer !== '') {
            yield $buffer;
        }
    }
}
