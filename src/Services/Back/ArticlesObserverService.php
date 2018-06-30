<?php

namespace InetStudio\Articles\Services\Back;

use InetStudio\Articles\Contracts\Models\ArticleModelContract;
use InetStudio\Articles\Contracts\Services\Back\ArticlesObserverServiceContract;

/**
 * Class ArticlesObserverService.
 */
class ArticlesObserverService implements ArticlesObserverServiceContract
{
    /**
     * @var
     */
    public $repository;

    /**
     * ArticlesObserverService constructor.
     */
    public function __construct()
    {
        $this->repository = app()->make('InetStudio\Articles\Contracts\Repositories\ArticlesRepositoryContract');
    }

    /**
     * Событие "объект создается".
     *
     * @param ArticleModelContract $item
     */
    public function creating(ArticleModelContract $item): void
    {
    }

    /**
     * Событие "объект создан".
     *
     * @param ArticleModelContract $item
     */
    public function created(ArticleModelContract $item): void
    {
    }

    /**
     * Событие "объект обновляется".
     *
     * @param ArticleModelContract $item
     */
    public function updating(ArticleModelContract $item): void
    {
    }

    /**
     * Событие "объект обновлен".
     *
     * @param ArticleModelContract $item
     */
    public function updated(ArticleModelContract $item): void
    {
    }

    /**
     * Событие "объект подписки удаляется".
     *
     * @param ArticleModelContract $item
     */
    public function deleting(ArticleModelContract $item): void
    {
    }

    /**
     * Событие "объект удален".
     *
     * @param ArticleModelContract $item
     */
    public function deleted(ArticleModelContract $item): void
    {
    }
}
