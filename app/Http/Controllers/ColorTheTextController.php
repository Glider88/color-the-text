<?php

namespace App\Http\Controllers;

use App\Services\StreamToLines;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Symfony\Component\Mercure\Hub;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Jwt\StaticTokenProvider;
use Symfony\Component\Mercure\Update;

class ColorTheTextController extends Controller
{
    public function upload(): View
    {
        return view('color.upload');
    }

    public function read(Request $request): View
    {
        $request->validate([
            'content' => ['required', 'max:10000'],
        ]);

        $text = $request->input("content", '');
        $this->llm();

        return view('color.read', ['content' => $text]);
    }

    private function llm()
    {
        $hub = $this->hub();

        $messages = [
            [
                'role' => 'user',
                'content' => 'tell a story',
            ],
        ];

        $response = Http::post('http://host.docker.internal:1234/v1/chat/completions', [
            "model" => "google/gemma-3-12b",
            "messages" => json_encode($messages, JSON_THROW_ON_ERROR),
            "temperature" => 0.7,
            "max_tokens" => -1,
            "stream" => true,
        ]);

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
            $result = $res['choices'][0]['delta']['content'] ?? '';

            $update = new Update('color-the-text', json_encode(['data' => $result], JSON_THROW_ON_ERROR));
            $hub->publish($update);
        }
    }

    private function jwtToken(): string
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

        return $jwtToken;
    }

    private function hub(): HubInterface
    {
        $jwtToken = $this->jwtToken();

        return new Hub("http://127.0.0.1:8000/.well-known/mercure", new StaticTokenProvider($jwtToken));
    }
}
