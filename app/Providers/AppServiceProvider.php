<?php

namespace App\Providers;

use App\RDF\EventParser;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if ($this->app->environment('local')) {
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        }

        $this->app->singleton(EventParser::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        require_once app_path('helpers.php');

        foreach (find('_components', resource_path('views')) as $componentsPath) {
            $ns = ($parts = explode('/', $componentsPath))[count($parts) - 2];

            Blade::anonymousComponentPath($componentsPath, $ns);
        }

        Blade::directive('hxCsrf', function () {
            return <<<__PHP__
                <?php echo '"_token": "' . csrf_token() . '"'; ?>
            __PHP__;
        });
    }
}
