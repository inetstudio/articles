<?php

namespace InetStudio\ArticlesPackage\Articles\Providers;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

/**
 * Class BindingsServiceProvider.
 */
class BindingsServiceProvider extends BaseServiceProvider implements DeferrableProvider
{
    /**
     * @var  array
     */
    public $bindings = [
        'InetStudio\ArticlesPackage\Articles\Contracts\Events\Back\ModifyItemEventContract' => 'InetStudio\ArticlesPackage\Articles\Events\Back\ModifyItemEvent',
        'InetStudio\ArticlesPackage\Articles\Contracts\Http\Controllers\Back\DataControllerContract' => 'InetStudio\ArticlesPackage\Articles\Http\Controllers\Back\DataController',
        'InetStudio\ArticlesPackage\Articles\Contracts\Http\Controllers\Back\ResourceControllerContract' => 'InetStudio\ArticlesPackage\Articles\Http\Controllers\Back\ResourceController',
        'InetStudio\ArticlesPackage\Articles\Contracts\Http\Controllers\Back\UtilityControllerContract' => 'InetStudio\ArticlesPackage\Articles\Http\Controllers\Back\UtilityController',
        'InetStudio\ArticlesPackage\Articles\Contracts\Http\Requests\Back\SaveItemRequestContract' => 'InetStudio\ArticlesPackage\Articles\Http\Requests\Back\SaveItemRequest',
        'InetStudio\ArticlesPackage\Articles\Contracts\Http\Responses\Back\Resource\DestroyResponseContract' => 'InetStudio\ArticlesPackage\Articles\Http\Responses\Back\Resource\DestroyResponse',
        'InetStudio\ArticlesPackage\Articles\Contracts\Http\Responses\Back\Resource\FormResponseContract' => 'InetStudio\ArticlesPackage\Articles\Http\Responses\Back\Resource\FormResponse',
        'InetStudio\ArticlesPackage\Articles\Contracts\Http\Responses\Back\Resource\IndexResponseContract' => 'InetStudio\ArticlesPackage\Articles\Http\Responses\Back\Resource\IndexResponse',
        'InetStudio\ArticlesPackage\Articles\Contracts\Http\Responses\Back\Resource\SaveResponseContract' => 'InetStudio\ArticlesPackage\Articles\Http\Responses\Back\Resource\SaveResponse',
        'InetStudio\ArticlesPackage\Articles\Contracts\Http\Responses\Back\Resource\ShowResponseContract' => 'InetStudio\ArticlesPackage\Articles\Http\Responses\Back\Resource\ShowResponse',
        'InetStudio\ArticlesPackage\Articles\Contracts\Http\Responses\Back\Utility\SlugResponseContract' => 'InetStudio\ArticlesPackage\Articles\Http\Responses\Back\Utility\SlugResponse',
        'InetStudio\ArticlesPackage\Articles\Contracts\Http\Responses\Back\Utility\SuggestionsResponseContract' => 'InetStudio\ArticlesPackage\Articles\Http\Responses\Back\Utility\SuggestionsResponse',
        'InetStudio\ArticlesPackage\Articles\Contracts\Models\ArticleModelContract' => 'InetStudio\ArticlesPackage\Articles\Models\ArticleModel',
        'InetStudio\ArticlesPackage\Articles\Contracts\Services\Back\DataTableServiceContract' => 'InetStudio\ArticlesPackage\Articles\Services\Back\DataTableService',
        'InetStudio\ArticlesPackage\Articles\Contracts\Services\Back\ItemsServiceContract' => 'InetStudio\ArticlesPackage\Articles\Services\Back\ItemsService',
        'InetStudio\ArticlesPackage\Articles\Contracts\Services\Back\UtilityServiceContract' => 'InetStudio\ArticlesPackage\Articles\Services\Back\UtilityService',
        'InetStudio\ArticlesPackage\Articles\Contracts\Services\Front\FeedsServiceContract' => 'InetStudio\ArticlesPackage\Articles\Services\Front\FeedsService',
        'InetStudio\ArticlesPackage\Articles\Contracts\Services\Front\ItemsServiceContract' => 'InetStudio\ArticlesPackage\Articles\Services\Front\ItemsService',
        'InetStudio\ArticlesPackage\Articles\Contracts\Services\Front\SitemapServiceContract' => 'InetStudio\ArticlesPackage\Articles\Services\Front\SitemapService',
        'InetStudio\ArticlesPackage\Articles\Contracts\Transformers\Back\Resource\IndexTransformerContract' => 'InetStudio\ArticlesPackage\Articles\Transformers\Back\Resource\IndexTransformer',
        'InetStudio\ArticlesPackage\Articles\Contracts\Transformers\Back\Utility\SuggestionTransformerContract' => 'InetStudio\ArticlesPackage\Articles\Transformers\Back\Utility\SuggestionTransformer',
        'InetStudio\ArticlesPackage\Articles\Contracts\Transformers\Front\Sitemap\ItemTransformerContract' => 'InetStudio\ArticlesPackage\Articles\Transformers\Front\Sitemap\ItemTransformer',
    ];

    /**
     * Получить сервисы от провайдера.
     *
     * @return array
     */
    public function provides()
    {
        return array_keys($this->bindings);
    }
}
