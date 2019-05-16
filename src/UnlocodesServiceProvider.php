<?php

namespace Dc\Unlocodes;

use Dc\Unlocodes\Helpers\UnlocodeHelper;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class UnlocodesServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');
        $this->loadRoutesFrom(__DIR__ . '/routes.php');
        Route::bind(
            'cachedUnlocode',
            function ($cachedUnlocode) {
                try {
                    return \Cache::rememberForever(
                        UnlocodeHelper::cacheKey($cachedUnlocode),
                        function () use ($cachedUnlocode) {
                            if ($model = $this->app->make(Unlocode::class)->resolveRouteBinding($cachedUnlocode)) {
                                return $model;
                            }

                            throw (new ModelNotFoundException)->setModel(Unlocode::class);
                        }
                    );
                } catch (\Exception $e) {
                    $httpBadRequest = 400;
                    return response()->json(['error' => $e->getMessage()], $httpBadRequest);
                }
            }
        );
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->make('Dc\Unlocodes\UnlocodeController');
        $this->registerHelpers();
    }

    /**
     * Register helpers file
     */
    public function registerHelpers()
    {
        // Load the helpers in src/helpers/
        if (file_exists($file = __DIR__ . '/helpers/UnlocodeHelper.php')) {
            include_once $file;
        }
    }
}
