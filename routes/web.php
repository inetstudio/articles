<?php

Route::group([
    'namespace' => 'InetStudio\Articles\Contracts\Http\Controllers\Back',
    'middleware' => ['web', 'back.auth'],
    'prefix' => 'back',
], function () {
    Route::any('articles/data', 'ArticlesDataControllerContract@data')->name('back.articles.data.index');
    Route::post('articles/slug', 'ArticlesUtilityControllerContract@getSlug')->name('back.articles.getSlug');
    Route::post('articles/suggestions', 'ArticlesUtilityControllerContract@getSuggestions')->name('back.articles.getSuggestions');

    Route::get('articles/create/{type?}', 'ArticlesControllerContract@create')->name('back.articles.create');
    Route::resource('articles', 'ArticlesControllerContract', ['except' => [
        'show', 'create',
    ], 'as' => 'back']);
});
