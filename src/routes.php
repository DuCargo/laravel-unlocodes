<?php
Route::pattern('unlocode', '^[A-Z]{2}[A-Z2-9]{3}$');
Route::group(
    [
        'prefix' => 'api',
        'middleware' => 'api',
    ],
    function () {
        Route::resource( 'unlocodes', 'dc\Unlocodes\UnlocodeController', [ 'except' => [ 'edit', 'create' ] ] );
        Route::get( 'unlocodes/search/{term}', 'dc\Unlocodes\UnlocodeController@search' );
        Route::group(
            [
                'prefix' => 'unlocodes',
            ],
            function () {
                Route::resource( 'groups', 'dc\Unlocodes\UnlocodeGroupController', [ 'except' => [ 'edit', 'create' ] ] );
                Route::get( 'groups/search/{term}', 'dc\Unlocodes\UnlocodeGroupController@search' );
            }
        );
    }
);
