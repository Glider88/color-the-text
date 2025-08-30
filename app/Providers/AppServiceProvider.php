<?php
declare(strict_types=1);

namespace App\Providers;

use App\Contract\LLM\OpenLikeApiInterface;
use App\Contract\LLM\TaskInterface;
use App\Contract\SSE\JwtTokenInterface;
use App\Contract\SSE\MercureHubInterface;
use App\Jobs\ProcessLlm;
use App\LLM\OpenLikeApi;
use App\LLM\SubjectPredicateTask;
use App\SSE\JwtToken;
use App\SSE\MercureHub;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $fn = fn(string $w, string $n, string $g) => $this->app->when($w)->needs($n)->give($g);

        $fn(ProcessLlm::class, MercureHubInterface::class, MercureHub::class);
        $fn(ProcessLlm::class, OpenLikeApiInterface::class, OpenLikeApi::class);
        $fn(ProcessLlm::class, TaskInterface::class, SubjectPredicateTask::class);

        $fn(MercureHub::class, JwtTokenInterface::class, JwtToken::class);
    }

    public function boot(): void
    {
        //
    }
}
