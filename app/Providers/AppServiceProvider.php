<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::before(function ($user, string $ability, array $arguments = []) {
            if (! $user->hasRole(['super_admin', 'Admin'])) {
                return null; // user biasa: lanjut ke policy
            }

            // Aturan khusus: Admin tidak boleh edit user dengan "detail" yang sama.
            // Biarkan UserPolicy@update yang memutuskan.
            if ($ability === 'update' && ($arguments[0] ?? null) instanceof \App\Models\User) {
                return null;
            }

            return true;
        });

        Gate::define('viewLogViewer', function ($user) {
            return $user && ($user->is_admin === true || $user->hasRole(['super_admin', 'Admin']));
        });
    }
}
