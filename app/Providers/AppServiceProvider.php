<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

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
        // Red de seguridad: si se usa SQLite en fichero y aún no existe,
        // se crea vacío para que `migrate:fresh --seed` funcione en un clon limpio
        // aunque se omita el `touch` del README.
        if (config('database.default') === 'sqlite') {
            $path = (string) config('database.connections.sqlite.database');
            if ($path !== '' && $path !== ':memory:' && ! file_exists($path)) {
                $dir = dirname($path);
                if (is_dir($dir) && is_writable($dir)) {
                    touch($path);
                }
            }
        }
    }
}
