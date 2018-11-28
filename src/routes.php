<?php
Route::pattern('unlocode', '^[A-Z]{2}[A-Z2-9]{3}$');
Route::group(
    [
        'prefix' => 'api',
        'middleware' => 'api',
    ],
    function () {
        Route::resource('unlocodes', 'Dc\Unlocodes\UnlocodeController', [ 'except' => [ 'edit', 'create', 'show' ] ]);
        Route::get('unlocodes/{cachedUnlocode}', 'Dc\Unlocodes\UnlocodeController@show');
        Route::get('unlocodes/search/{term}', 'Dc\Unlocodes\UnlocodeController@search');
        Route::group(
            [
                'prefix' => 'unlocodes',
            ],
            function () {
                Route::resource('groups', 'Dc\Unlocodes\UnlocodeGroupController', [ 'except' => [ 'edit', 'create' ] ]);
                Route::get('groups/search/{term}', 'Dc\Unlocodes\UnlocodeGroupController@search');
            }
        );
    }
);
