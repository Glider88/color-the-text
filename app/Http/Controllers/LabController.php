<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
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
}

class A
{
    public static int $a = 0;
}
