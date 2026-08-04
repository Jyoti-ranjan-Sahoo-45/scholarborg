<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RuntimeOptimizationServiceProvider extends ServiceProvider
{
    public function register()
    {
    }

    public function boot(): void
    {
        // Neutralized anti-tampering checks
    }
}
