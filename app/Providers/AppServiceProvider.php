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
        // `php artisan serve` filtra las variables de entorno que hereda el
        // servidor. Estas dos solo existen en instalaciones PHP portables
        // (configuradas por variables) y no tienen ningún efecto cuando no
        // están definidas.
        if (class_exists(\Illuminate\Foundation\Console\ServeCommand::class)) {
            foreach (['PHPRC', 'LD_LIBRARY_PATH'] as $variable) {
                if (! in_array($variable, \Illuminate\Foundation\Console\ServeCommand::$passthroughVariables, true)) {
                    \Illuminate\Foundation\Console\ServeCommand::$passthroughVariables[] = $variable;
                }
            }
        }
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
