<?php declare(strict_types=1);

namespace App\SSE;

use App\Contract\SSE\JwtTokenInterface;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Config\MercureConfig;

class JwtToken implements JwtTokenInterface
{
    private string $jwtToken = '';

    public function __construct(
        private readonly MercureConfig $mercureConfig,
    ) {}

    public function token(): string
    {
        if (! empty($this->jwtToken)) {
            return $this->jwtToken;
        }

        $jwtConfiguration = Configuration::forSymmetricSigner(
            new Sha256(),
            InMemory::plainText($this->mercureConfig->jwtSecret)
        );

        $this->jwtToken = $jwtConfiguration
            ->builder()
            ->withClaim('mercure', ['publish' => ['*']])
            ->getToken($jwtConfiguration->signer(), $jwtConfiguration->signingKey())
            ->toString();

        return $this->jwtToken;
    }
}
