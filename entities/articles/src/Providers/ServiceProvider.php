<?php

namespace InetStudio\ArticlesPackage\Articles\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

/**
 * Class ServiceProvider.
 */
class ServiceProvider extends BaseServiceProvider
{
    /**
     * Загрузка сервиса.
     */
    public function boot(): void
    {
        $this->registerConsoleCommands();
        $this->registerPublishes();
        $this->registerRoutes();
        $this->registerViews();
    }

    /**
     * Регистрация команд.
     */
    protected function registerConsoleCommands(): void
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->commands([
            'InetStudio\ArticlesPackage\Articles\Console\Commands\SetupCommand',
            'InetStudio\ArticlesPackage\Articles\Console\Commands\CreateFoldersCommand',
            'InetStudio\ArticlesPackage\Articles\Console\Commands\CreateArticleTypeCommand',
        ]);
    }

    /**
     * Регистрация ресурсов.
     */
    protected function registerPublishes(): void
    {
        $this->publishes(
            [
                __DIR__.'/../../config/articles.php' => config_path('articles.php'),
            ],
            'config'
        );

        $this->mergeConfigFrom(
            __DIR__.'/../../config/filesystems.php',
            'filesystems.disks'
        );

        if (! $this->app->runningInConsole()) {
            return;
        }

        if (Schema::hasTable('articles')) {
            return;
        }

        $timestamp = date('Y_m_d_His', time());
        $this->publishes(
            [
                __DIR__.'/../../database/migrations/create_articles_tables.php.stub' => database_path('migrations/'.$timestamp.'_create_articles_tables.php'),
            ],
            'migrations'
        );
    }

    /**
     * Регистрация путей.
     */
    protected function registerRoutes(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../../routes/web.php');
    }

    /**
     * Регистрация представлений.
     */
    protected function registerViews(): void
    {
        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'admin.module.articles');
    }
}
