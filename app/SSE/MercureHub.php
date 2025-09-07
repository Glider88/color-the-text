<?php declare(strict_types=1);

namespace App\SSE;

use App\Contract\SSE\JwtTokenInterface;
use App\Contract\SSE\MercureHubInterface;
use Illuminate\Container\Attributes\Give;
use MercureConfig;
use Symfony\Component\Mercure\Hub;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Jwt\StaticTokenProvider;
use Symfony\Component\Mercure\Update;

class MercureHub implements MercureHubInterface
{
    private ?HubInterface $hub = null;

    public function __construct(
        #[Give(JwtToken::class)] private readonly JwtTokenInterface $jwtToken,
        private readonly MercureConfig $mercureConfig,
    ) {}

    public function publish(int $topicId, string $data): void
    {
        $token = $this->jwtToken->token();
        if ($this->hub === null) {
            $this->hub = new Hub($this->mercureConfig->url, new StaticTokenProvider($token));
        }

        $prefix = $this->mercureConfig->topicPrefix;

        $update = new Update($prefix . $topicId, $data);
        $this->hub->publish($update);
    }
}
