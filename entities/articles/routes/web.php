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
                    'create',
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
        Route::get('/{material_type}/{slug}/export/comments', 'ExportControllerContract@exportComments')
            ->where('material_type', '^(?!battle|contest|ingredient).*$')
            ->name('front.articles.export.comments');

        Route::get('/{material_type}/{slug}/export/images', 'ExportControllerContract@exportImages')
            ->where('material_type', '^(?!battle|contest|ingredient).*$')
            ->name('front.articles.export.images');
    }
);
