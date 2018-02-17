<?php

namespace InetStudio\Articles\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use InetStudio\Articles\Models\ArticleModel;
use InetStudio\Articles\Events\ModifyArticleEvent;
use InetStudio\Articles\Console\Commands\SetupCommand;
use InetStudio\Articles\Services\Front\ArticlesService;
use InetStudio\Articles\Listeners\ClearArticlesCacheListener;
use InetStudio\Articles\Console\Commands\CreateFoldersCommand;
use InetStudio\Articles\Http\Requests\Back\SaveArticleRequest;
use InetStudio\Articles\Http\Controllers\Back\ArticlesController;
use InetStudio\Articles\Contracts\Events\ModifyArticleEventContract;
use InetStudio\Articles\Http\Controllers\Back\ArticlesDataController;
use InetStudio\Articles\Http\Controllers\Back\ArticlesUtilityController;
use InetStudio\Articles\Contracts\Services\Front\ArticlesServiceContract;
use InetStudio\Articles\Contracts\Http\Requests\Back\SaveArticleRequestContract;
use InetStudio\Articles\Contracts\Http\Controllers\Back\ArticlesControllerContract;
use InetStudio\Articles\Contracts\Http\Controllers\Back\ArticlesDataControllerContract;
use InetStudio\Articles\Contracts\Http\Controllers\Back\ArticlesUtilityControllerContract;

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
        $this->registerEvents();
        $this->registerViewComposers();
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
                SetupCommand::class,
                CreateFoldersCommand::class,
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
     * Регистрация событий.
     *
     * @return void
     */
    protected function registerEvents(): void
    {
        Event::listen(ModifyArticleEventContract::class, ClearArticlesCacheListener::class);
    }

    /**
     * Register Article's view composers.
     *
     * @return void
     */
    public function registerViewComposers(): void
    {
        view()->composer('admin.module.articles::back.partials.analytics.materials.statistic', function ($view) {
            $articles = ArticleModel::with('status')->select(['status_id', DB::raw('count(*) as total')])->groupBy('status_id')->get();

            $view->with('articles', $articles);
        });
    }

    /**
     * Регистрация привязок, алиасов и сторонних провайдеров сервисов.
     *
     * @return void
     */
    protected function registerBindings(): void
    {
        // Controllers
        $this->app->bind(ArticlesControllerContract::class, ArticlesController::class);
        $this->app->bind(ArticlesDataControllerContract::class, ArticlesDataController::class);
        $this->app->bind(ArticlesUtilityControllerContract::class, ArticlesUtilityController::class);

        // Events
        $this->app->bind(ModifyArticleEventContract::class, ModifyArticleEvent::class);

        // Requests
        $this->app->bind(SaveArticleRequestContract::class, SaveArticleRequest::class);

        // Services
        $this->app->bind(ArticlesServiceContract::class, ArticlesService::class);
    }
}
