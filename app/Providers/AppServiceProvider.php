<?php
declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
//        $fn = fn(string $w, string $n, string $g) => $this->app->when($w)->needs($n)->give($g);
    }

    public function boot(): void
    {
        //
    }
}
