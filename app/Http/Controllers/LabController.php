<?php

namespace App\Http\Controllers;

use Http;
use Illuminate\View\View;
use Generator;
use Psr\Http\Message\StreamInterface;
use Symfony\Component\Mercure\Hub;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Jwt\StaticTokenProvider;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Symfony\Component\Mercure\Update;

class LabController extends Controller
{
    public function count(): View
    {
        A::$a += 1;

        return view('count', ['a' => A::$a]);
    }

    public function lab(): View
    {
        return view('lab');
    }

    public function ping()
    {
        $jwtConfiguration = Configuration::forSymmetricSigner(
            new Sha256(),
            InMemory::plainText(config('octane.mercure.publisher_jwt'))
        );

        $jwtToken = $jwtConfiguration
            ->builder()
            ->withClaim('mercure', ['publish' => ['*']])
            ->getToken($jwtConfiguration->signer(), $jwtConfiguration->signingKey())
            ->toString();

        /** @var HubInterface $hub */
        $hub = new Hub("http://127.0.0.1:8000/.well-known/mercure", new StaticTokenProvider($jwtToken));
        $update = new Update('lab', json_encode(['data' => 'ping'], JSON_THROW_ON_ERROR));
        $hub->publish($update);


        return response()->json([]);
    }

    public function llm()
    {
        $messages = [
            ['role' => 'user',       'content' => 'say "yes"',],
            ['role' => 'assistant',  'content' => 'Yes! 😊',],
            ['role' => 'user',       'content' => 'say "no"',],
            ['role' => 'assistant',  'content' => 'No.',],
            ['role' => 'user',       'content' => 'what I asked you at the very beginning?',],
        ];

        $response = Http::post('http://host.docker.internal:1234/v1/chat/completions', [
            "model" => "google/gemma-3-12b",
            "messages" => json_encode($messages, JSON_THROW_ON_ERROR),
            "temperature" => 0.7,
            "max_tokens" => -1,
            "stream" => true,
        ]);

//        dd($response->getBody()->getContents());

        $result = [];
        $body = $response->toPsrResponse()->getBody();
        $s2l = new StreamToLines($body);
        foreach ($s2l->lines() as $line) {
            if (empty($line)) {
                continue;
            }

            $start = strpos($line, "{");
            $end = strrpos($line, "}");
            if ($start === false || $end === false) {
                continue;
            }

            $line = substr($line, $start, $end - $start + 1);

            $res = json_decode($line, associative: true, flags: JSON_THROW_ON_ERROR);
            $result[] = $res['choices'][0]['delta']['content'] ?? '';
        }

        dd($result);

        return response()->json([]);
    }
}

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

class A
{
    public static int $a = 0;
}
