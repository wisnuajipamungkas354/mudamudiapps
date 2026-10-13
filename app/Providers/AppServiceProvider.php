<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Support\Facades\Gate;
use Opcodes\LogViewer\Facades\LogViewer;

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

        LogViewer::auth(function ($request) {
            $user = $request->user();

            return $user && ($user->is_admin == true || (method_exists($user, 'hasRole') && $user->hasRole(['super_admin', 'Admin'])));
        });

        Gate::define('viewLogViewer', function ($user = null) {
            $user = $user ?? auth()->user();

            return $user && ($user->is_admin == true || (method_exists($user, 'hasRole') && $user->hasRole(['super_admin', 'Admin'])));
        });
    }
}
