<?php

namespace D8vjork\LaravelOptimizely;

use D8vjork\LaravelOptimizely\Console\GetDatafileCommand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Optimizely\Optimizely;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/optimizely.php' => config_path('optimizely.php'),
        ], 'config');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerCommands();

        if (file_exists(config('optimizely.datafile_path'))) {
            $this->app->singleton(Optimizely::class, function ($app) {
                return new Optimizely(file_get_contents(config('optimizely.datafile_path')));
            });

            Blade::if('feature', function ($feature) {
                return $this->app[Optimizely::class]->isFeatureEnabled($feature, Auth::id() ?: '');
            });
        }
    }

    protected function registerRoutes()
    {
        Route::post($this->routeConfiguration(), function (Request $request) {
            $hmac = hash_hmac('sha1', config('optimizely.webhook.secret'), json_encode($request->all()));

            if (! hash_equals("sha1=${hmac}", $request->header('X-Hub-Signature'))) {
                abort(500);
            }

            Artisan::call(GetDatafileCommand::class);
        });
    }

    /**
     * Get the Telescope route group configuration array.
     *
     * @return array
     */
    protected function routeConfiguration()
    {
        return [
            'domain' => config('optimizely.webhook_path', '/webhooks/optimizely'),
        ];
    }

    protected function registerCommands()
    {
        $this->app->bind('command.optimizely:datafile', GetDatafileCommand::class);

        $this->commands(['command.optimizely:datafile']);
    }
}
