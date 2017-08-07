<?php

Route::group(['namespace' => 'InetStudio\Articles\Controllers'], function () {
    Route::group(['middleware' => 'web', 'prefix' => 'back'], function () {
        Route::group(['middleware' => 'back.auth'], function () {
            Route::post('articles/slug', 'ArticlesController@getSlug')->name('back.articles.getSlug');
            Route::any('articles/data', 'ArticlesController@data')->name('back.articles.data');
            Route::resource('articles', 'ArticlesController', ['except' => [
                'show',
            ], 'as' => 'back']);
        });
    });
});
