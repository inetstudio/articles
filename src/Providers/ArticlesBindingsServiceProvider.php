<?php

namespace InetStudio\Articles\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * Class ArticlesBindingsServiceProvider.
 */
class ArticlesBindingsServiceProvider extends ServiceProvider
{
    /**
    * @var  bool
    */
    protected $defer = true;

    /**
    * @var  array
    */
    public $bindings = [
        'InetStudio\Articles\Contracts\Events\Back\ModifyArticleEventContract' => 'InetStudio\Articles\Events\Back\ModifyArticleEvent',
        'InetStudio\Articles\Contracts\Http\Controllers\Back\ArticlesControllerContract' => 'InetStudio\Articles\Http\Controllers\Back\ArticlesController',
        'InetStudio\Articles\Contracts\Http\Controllers\Back\ArticlesDataControllerContract' => 'InetStudio\Articles\Http\Controllers\Back\ArticlesDataController',
        'InetStudio\Articles\Contracts\Http\Controllers\Back\ArticlesUtilityControllerContract' => 'InetStudio\Articles\Http\Controllers\Back\ArticlesUtilityController',
        'InetStudio\Articles\Contracts\Http\Requests\Back\SaveArticleRequestContract' => 'InetStudio\Articles\Http\Requests\Back\SaveArticleRequest',
        'InetStudio\Articles\Contracts\Http\Responses\Back\Articles\DestroyResponseContract' => 'InetStudio\Articles\Http\Responses\Back\Articles\DestroyResponse',
        'InetStudio\Articles\Contracts\Http\Responses\Back\Articles\FormResponseContract' => 'InetStudio\Articles\Http\Responses\Back\Articles\FormResponse',
        'InetStudio\Articles\Contracts\Http\Responses\Back\Articles\IndexResponseContract' => 'InetStudio\Articles\Http\Responses\Back\Articles\IndexResponse',
        'InetStudio\Articles\Contracts\Http\Responses\Back\Articles\SaveResponseContract' => 'InetStudio\Articles\Http\Responses\Back\Articles\SaveResponse',
        'InetStudio\Articles\Contracts\Http\Responses\Back\Articles\ShowResponseContract' => 'InetStudio\Articles\Http\Responses\Back\Articles\ShowResponse',
        'InetStudio\Articles\Contracts\Http\Responses\Back\Utility\SlugResponseContract' => 'InetStudio\Articles\Http\Responses\Back\Utility\SlugResponse',
        'InetStudio\Articles\Contracts\Http\Responses\Back\Utility\SuggestionsResponseContract' => 'InetStudio\Articles\Http\Responses\Back\Utility\SuggestionsResponse',
        'InetStudio\Articles\Contracts\Models\ArticleModelContract' => 'InetStudio\Articles\Models\ArticleModel',
        'InetStudio\Articles\Contracts\Observers\ArticleObserverContract' => 'InetStudio\Articles\Observers\ArticleObserver',
        'InetStudio\Articles\Contracts\Repositories\ArticlesRepositoryContract' => 'InetStudio\Articles\Repositories\ArticlesRepository',
        'InetStudio\Articles\Contracts\Services\Back\ArticlesDataTableServiceContract' => 'InetStudio\Articles\Services\Back\ArticlesDataTableService',
        'InetStudio\Articles\Contracts\Services\Back\ArticlesObserverServiceContract' => 'InetStudio\Articles\Services\Back\ArticlesObserverService',
        'InetStudio\Articles\Contracts\Services\Back\ArticlesServiceContract' => 'InetStudio\Articles\Services\Back\ArticlesService',
        'InetStudio\Articles\Contracts\Transformers\Back\ArticleTransformerContract' => 'InetStudio\Articles\Transformers\Back\ArticleTransformer',
        'InetStudio\Articles\Contracts\Transformers\Back\SuggestionTransformerContract' => 'InetStudio\Articles\Transformers\Back\SuggestionTransformer',
        'InetStudio\Articles\Contracts\Transformers\Front\ArticlesFeedItemsTransformerContract' => 'InetStudio\Articles\Transformers\Front\ArticlesFeedItemsTransformer',
        'InetStudio\Articles\Contracts\Transformers\Front\ArticlesSiteMapTransformerContract' => 'InetStudio\Articles\Transformers\Front\ArticlesSiteMapTransformer',
        'InetStudio\Articles\Contracts\Transformers\Front\Feeds\Mindbox\ArticleTransformerContract' => 'InetStudio\Articles\Transformers\Front\Feeds\Mindbox\ArticleTransformer',
    ];

    /**
     * @var  array
     */
    public $singletons = [
        'InetStudio\Articles\Contracts\Services\Front\ArticlesServiceContract' => 'InetStudio\Articles\Services\Front\ArticlesService',
    ];

    /**
     * Получить сервисы от провайдера.
     *
     * @return  array
     */
    public function provides()
    {
        return array_merge(
            array_keys($this->bindings),
            array_keys($this->singletons)
        );
    }
}
