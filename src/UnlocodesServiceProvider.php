<?php

namespace Dc\Unlocodes;

use Dc\Unlocodes\Facades\Unlocode;
use Dc\Unlocodes\Helpers\UnlocodeHelper;
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
            'unlocode',
            function ($value) {
                try {
                    [$countrycode, $placecode] = UnlocodeHelper::spreadUnlocode($value);
                    $value = \Cache::rememberForever(
                        UnlocodeHelper::cacheKey($countrycode, $placecode),
                        function () use ($countrycode, $placecode) {
                            return Unlocode::where(
                                [
                                'countrycode' => $countrycode,
                                'placecode' => $placecode,
                                ]
                            )->firstOrFail();
                        }
                    );
                    return $value;
                } catch (\Exception $e) {
                    return response()->json(['error' => $e->getMessage()], 400);
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
