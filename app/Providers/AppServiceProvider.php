<?php

namespace App\Providers;

use App\Projectors\LabelProjector;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;
use Spatie\EventSourcing\Projectionist;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Inertia::setRootView('app');
        $this->app->make(Projectionist::class)->addProjector(LabelProjector::class);
    }
}
