<?php

namespace NotFound\ListBoss;

use Illuminate\Support\ServiceProvider;

class ListBossServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
        $this->publishes([
            __DIR__.'/../config/listboss.php' => config_path('listboss.php'),
        ], 'siteboss-listboss');
    }
}
