<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'namespace' => 'InetStudio\ArticlesPackage\Articles\Contracts\Http\Controllers\Back',
        'middleware' => ['web', 'back.auth'],
        'prefix' => 'back',
    ],
    function () {
        Route::any('articles/data', 'DataControllerContract@data')
            ->name('back.articles.data.index');

        Route::post('articles/slug', 'UtilityControllerContract@getSlug')
            ->name('back.articles.getSlug');

        Route::post('articles/suggestions', 'UtilityControllerContract@getSuggestions')
            ->name('back.articles.getSuggestions');

        Route::get('articles/create/{type?}', 'ResourceControllerContract@create')->name('back.articles.create');
        Route::resource(
            'articles',
            'ResourceControllerContract',
            [
                'except' => [
                    'show', 'create',
                ],
                'as' => 'back',
            ]
        );
    }
);

Route::group(
    [
        'namespace' => 'InetStudio\ArticlesPackage\Articles\Contracts\Http\Controllers\Front',
        'middleware' => ['web'],
    ],
    function () {
        Route::get('/article/{slug}/export/comments', 'ExportController@exportComments')->name('front.articles.export.comments');
        Route::get('/article/{slug}/export/images', 'ExportController@exportImages')->name('front.articles.export.images');
    }
);
