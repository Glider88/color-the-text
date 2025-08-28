<?php declare(strict_types=1);

namespace App\Helper;

use Psr\Http\Message\StreamInterface;

readonly class IteratorHelper
{
    private const int DEFAULT_STREAM_CHUNK_SIZE = 8192;

    /**
     * @template A
     * @template B
     * @param iterable<A> $iterator
     * @param callable(A): B $callback
     * @return iterable<B>
     */
    public static function map(iterable $iterator, callable $callback): iterable
    {
        foreach ($iterator as $key => $value) {
            yield $key => $callback($value);
        }
    }

    /**
     * @param iterable<string> $iterator
     * @return iterable<string>
     */
    public static function lines(iterable $iterator): iterable
    {
        $buffer = '';
        foreach ($iterator as $value) {
            $buffer .= $value;
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

    /** @return iterable<string> */
    public static function fromStream(
        StreamInterface $stream,
        int $chunkSize = self::DEFAULT_STREAM_CHUNK_SIZE
    ): iterable
    {
        while (!$stream->eof()) {
            yield $stream->read($chunkSize);
        }
    }
}
