<?php

namespace InetStudio\Articles\Observers;

use InetStudio\Articles\Contracts\Models\ArticleModelContract;
use InetStudio\Articles\Contracts\Observers\ArticleObserverContract;

/**
 * Class ArticleObserver.
 */
class ArticleObserver implements ArticleObserverContract
{
    /**
     * Используемые сервисы.
     *
     * @var array
     */
    protected $services;

    /**
     * ArticleObserver constructor.
     */
    public function __construct()
    {
        $this->services['articlesObserver'] = app()->make('InetStudio\Articles\Contracts\Services\Back\ArticlesObserverServiceContract');
    }

    /**
     * Событие "объект создается".
     *
     * @param ArticleModelContract $item
     */
    public function creating(ArticleModelContract $item): void
    {
        $this->services['articlesObserver']->creating($item);
    }

    /**
     * Событие "объект создан".
     *
     * @param ArticleModelContract $item
     */
    public function created(ArticleModelContract $item): void
    {
        $this->services['articlesObserver']->created($item);
    }

    /**
     * Событие "объект обновляется".
     *
     * @param ArticleModelContract $item
     */
    public function updating(ArticleModelContract $item): void
    {
        $this->services['articlesObserver']->updating($item);
    }

    /**
     * Событие "объект обновлен".
     *
     * @param ArticleModelContract $item
     */
    public function updated(ArticleModelContract $item): void
    {
        $this->services['articlesObserver']->updated($item);
    }

    /**
     * Событие "объект подписки удаляется".
     *
     * @param ArticleModelContract $item
     */
    public function deleting(ArticleModelContract $item): void
    {
        $this->services['articlesObserver']->deleting($item);
    }

    /**
     * Событие "объект удален".
     *
     * @param ArticleModelContract $item
     */
    public function deleted(ArticleModelContract $item): void
    {
        $this->services['articlesObserver']->deleted($item);
    }
}
