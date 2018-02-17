<?php

use InetStudio\Articles\Contracts\Http\Controllers\Back\ArticlesControllerContract;
use InetStudio\Articles\Contracts\Http\Controllers\Back\ArticlesDataControllerContract;
use InetStudio\Articles\Contracts\Http\Controllers\Back\ArticlesUtilityControllerContract;

Route::group([
    'middleware' => ['web', 'back.auth'],
    'prefix' => 'back',
], function () {
    Route::post('articles/slug', ArticlesUtilityControllerContract::class.'@getSlug')->name('back.articles.getSlug');
    Route::post('articles/suggestions', ArticlesUtilityControllerContract::class.'@getSuggestions')->name('back.articles.getSuggestions');
    Route::any('articles/data', ArticlesDataControllerContract::class.'@data')->name('back.articles.data');
    Route::resource('articles', ArticlesControllerContract::class, ['except' => [
        'show',
    ], 'as' => 'back']);
});
