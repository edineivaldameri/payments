<?php

declare(strict_types=1);

namespace EdineiValdameri\Pagamentos\Providers;

use Illuminate\Support\ServiceProvider;

class PaymentsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->loadMigrationsFrom([
                __DIR__ . '/../../database/migrations',
            ]);

            $this->publishes([
                __DIR__ . '/../config/pagamentos.php' => config_path('pagamentos.php'),
            ], 'pagamentos-config');
        }
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/pagamentos.php', 'pagamentos');
    }
}
