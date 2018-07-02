<?php

namespace InetStudio\Articles\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * Class ArticlesServiceProvider.
 */
class ArticlesServiceProvider extends ServiceProvider
{
    /**
     * Загрузка сервиса.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->registerConsoleCommands();
        $this->registerPublishes();
        $this->registerRoutes();
        $this->registerViews();
        $this->registerObservers();
    }

    /**
     * Регистрация привязки в контейнере.
     *
     * @return void
     */
    public function register(): void
    {
        $this->registerBindings();
    }

    /**
     * Регистрация команд.
     *
     * @return void
     */
    protected function registerConsoleCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                'InetStudio\Articles\Console\Commands\SetupCommand',
                'InetStudio\Articles\Console\Commands\CreateFoldersCommand',
                'InetStudio\Articles\Console\Commands\CreateArticleTypeCommand',
            ]);
        }
    }

    /**
     * Регистрация ресурсов.
     *
     * @return void
     */
    protected function registerPublishes(): void
    {
        $this->publishes([
            __DIR__.'/../../config/articles.php' => config_path('articles.php'),
        ], 'config');

        $this->mergeConfigFrom(
            __DIR__.'/../../config/filesystems.php', 'filesystems.disks'
        );

        if ($this->app->runningInConsole()) {
            if (! class_exists('CreateArticlesTables')) {
                $timestamp = date('Y_m_d_His', time());
                $this->publishes([
                    __DIR__.'/../../database/migrations/create_articles_tables.php.stub' => database_path('migrations/'.$timestamp.'_create_articles_tables.php'),
                ], 'migrations');
            }
        }
    }

    /**
     * Регистрация путей.
     *
     * @return void
     */
    protected function registerRoutes(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../../routes/web.php');
    }

    /**
     * Регистрация представлений.
     *
     * @return void
     */
    protected function registerViews(): void
    {
        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'admin.module.articles');
    }

    /**
     * Регистрация наблюдателей.
     *
     * @return void
     */
    public function registerObservers(): void
    {
        $this->app->make('InetStudio\Articles\Contracts\Models\ArticleModelContract')::observe($this->app->make('InetStudio\Articles\Contracts\Observers\ArticleObserverContract'));
    }

    /**
     * Регистрация привязок, алиасов и сторонних провайдеров сервисов.
     *
     * @return void
     */
    protected function registerBindings(): void
    {
        // Controllers
        $this->app->bind('InetStudio\Articles\Contracts\Http\Controllers\Back\ArticlesControllerContract', 'InetStudio\Articles\Http\Controllers\Back\ArticlesController');
        $this->app->bind('InetStudio\Articles\Contracts\Http\Controllers\Back\ArticlesDataControllerContract', 'InetStudio\Articles\Http\Controllers\Back\ArticlesDataController');
        $this->app->bind('InetStudio\Articles\Contracts\Http\Controllers\Back\ArticlesUtilityControllerContract', 'InetStudio\Articles\Http\Controllers\Back\ArticlesUtilityController');

        // Events
        $this->app->bind('InetStudio\Articles\Contracts\Events\Back\ModifyArticleEventContract', 'InetStudio\Articles\Events\Back\ModifyArticleEvent');

        // Models
        $this->app->bind('InetStudio\Articles\Contracts\Models\ArticleModelContract', 'InetStudio\Articles\Models\ArticleModel');

        // Observers
        $this->app->bind('InetStudio\Articles\Contracts\Observers\ArticleObserverContract', 'InetStudio\Articles\Observers\ArticleObserver');

        // Repositories
        $this->app->bind('InetStudio\Articles\Contracts\Repositories\ArticlesRepositoryContract', 'InetStudio\Articles\Repositories\ArticlesRepository');

        // Requests
        $this->app->bind('InetStudio\Articles\Contracts\Http\Requests\Back\SaveArticleRequestContract', 'InetStudio\Articles\Http\Requests\Back\SaveArticleRequest');

        // Responses
        $this->app->bind('InetStudio\Articles\Contracts\Http\Responses\Back\Articles\DestroyResponseContract', 'InetStudio\Articles\Http\Responses\Back\Articles\DestroyResponse');
        $this->app->bind('InetStudio\Articles\Contracts\Http\Responses\Back\Articles\FormResponseContract', 'InetStudio\Articles\Http\Responses\Back\Articles\FormResponse');
        $this->app->bind('InetStudio\Articles\Contracts\Http\Responses\Back\Articles\IndexResponseContract', 'InetStudio\Articles\Http\Responses\Back\Articles\IndexResponse');
        $this->app->bind('InetStudio\Articles\Contracts\Http\Responses\Back\Articles\SaveResponseContract', 'InetStudio\Articles\Http\Responses\Back\Articles\SaveResponse');
        $this->app->bind('InetStudio\Articles\Contracts\Http\Responses\Back\Articles\ShowResponseContract', 'InetStudio\Articles\Http\Responses\Back\Articles\ShowResponse');
        $this->app->bind('InetStudio\Articles\Contracts\Http\Responses\Back\Utility\SlugResponseContract', 'InetStudio\Articles\Http\Responses\Back\Utility\SlugResponse');
        $this->app->bind('InetStudio\Articles\Contracts\Http\Responses\Back\Utility\SuggestionsResponseContract', 'InetStudio\Articles\Http\Responses\Back\Utility\SuggestionsResponse');

        // Services
        $this->app->bind('InetStudio\Articles\Contracts\Services\Back\ArticlesDataTableServiceContract', 'InetStudio\Articles\Services\Back\ArticlesDataTableService');
        $this->app->bind('InetStudio\Articles\Contracts\Services\Back\ArticlesObserverServiceContract', 'InetStudio\Articles\Services\Back\ArticlesObserverService');
        $this->app->bind('InetStudio\Articles\Contracts\Services\Back\ArticlesServiceContract', 'InetStudio\Articles\Services\Back\ArticlesService');
        $this->app->bind('InetStudio\Articles\Contracts\Services\Front\ArticlesServiceContract', 'InetStudio\Articles\Services\Front\ArticlesService');

        // Transformers
        $this->app->bind('InetStudio\Articles\Contracts\Transformers\Back\ArticleTransformerContract', 'InetStudio\Articles\Transformers\Back\ArticleTransformer');
        $this->app->bind('InetStudio\Articles\Contracts\Transformers\Back\SuggestionTransformerContract', 'InetStudio\Articles\Transformers\Back\SuggestionTransformer');
        $this->app->bind('InetStudio\Articles\Contracts\Transformers\Front\ArticlesFeedItemsTransformerContract', 'InetStudio\Articles\Transformers\Front\ArticlesFeedItemsTransformer');
        $this->app->bind('InetStudio\Articles\Contracts\Transformers\Front\ArticlesSiteMapTransformerContract', 'InetStudio\Articles\Transformers\Front\ArticlesSiteMapTransformer');
    }
}
