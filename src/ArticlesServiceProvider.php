<?php

namespace InetStudio\Articles;

use Illuminate\Support\ServiceProvider;

class ArticlesServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views/admin', 'admin.module.articles');
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        $this->publishes([
            __DIR__.'/../config/articles.php' => config_path('articles.php'),
        ], 'config');

        $this->mergeConfigFrom(
            __DIR__.'/../config/filesystems.php', 'filesystems.disks'
        );

        if ($this->app->runningInConsole()) {
            if (! class_exists('CreateArticlesTables')) {
                $timestamp = date('Y_m_d_His', time());
                $this->publishes([
                    __DIR__.'/../database/migrations/create_articles_tables.php.stub' => database_path('migrations/'.$timestamp.'_create_articles_tables.php'),
                ], 'migrations');
            }

            $this->commands([
                Commands\SetupCommand::class,
                Commands\CreateFoldersCommand::class,
            ]);
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {

    }
}
