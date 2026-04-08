<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Bloquear acciones Livewire cuando el trial expiró
        Livewire::setUpdateRoute(function ($handle) {
            return \Illuminate\Support\Facades\Route::post('/livewire/update', $handle)
                ->middleware(['web', 'auth', 'trial.expirado'])
                ->name('livewire.update');
        });
    }
}
