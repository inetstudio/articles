<?php

namespace InetStudio\Articles\Services\Front;

use League\Fractal\Manager;
use League\Fractal\Serializer\DataArraySerializer;
use InetStudio\Articles\Contracts\Services\Front\ArticlesServiceContract;
use InetStudio\Articles\Contracts\Repositories\ArticlesRepositoryContract;

/**
 * Class ArticlesService.
 */
class ArticlesService implements ArticlesServiceContract
{
    /**
     * @var ArticlesRepositoryContract
     */
    private $repository;

    /**
     * ArticlesService constructor.
     *
     * @param ArticlesRepositoryContract $repository
     */
    public function __construct(ArticlesRepositoryContract $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Получаем объект по id.
     *
     * @param int $id
     *
     * @return mixed
     */
    public function getArticleById(int $id = 0)
    {
        return $this->repository->getItemByID($id);
    }

    /**
     * Получаем объект по slug.
     *
     * @param string $slug
     * @param bool $returnBuilder
     *
     * @return mixed
     */
    public function getArticleBySlug(string $slug, bool $returnBuilder = false)
    {
        return $this->repository->getItemBySlug($slug, $returnBuilder);
    }

    /**
     * Получаем все объекты.
     *
     * @param bool $returnBuilder
     *
     * @return mixed
     */
    public function getAllArticles(bool $returnBuilder = false)
    {
        return $this->repository->getAllItems($returnBuilder);
    }

    /**
     * Получаем информацию по ингредиентам для фида.
     *
     * @return array
     */
    public function getFeedItems(): array
    {
        $items = $this->repository->getAllItems(true)
            ->whereNotNull('publish_date')
            ->orderBy('publish_date', 'desc')
            ->limit(500)
            ->get();

        $resource = app()->make('InetStudio\Articles\Contracts\Transformers\Front\ArticlesFeedItemsTransformerContract')
            ->transformCollection($items);

        $manager = new Manager();
        $manager->setSerializer(new DataArraySerializer());

        $transformation = $manager->createData($resource)->toArray();

        return $transformation['data'];
    }

    /**
     * Получаем информацию по ингредиентам для карты сайта.
     *
     * @return array
     */
    public function getSiteMapItems(): array
    {
        $items = $this->repository->getAllItems();

        $resource = app()->make('InetStudio\Articles\Contracts\Transformers\Front\ArticlesSiteMapTransformerContract')
            ->transformCollection($items);

        $manager = new Manager();
        $manager->setSerializer(new DataArraySerializer());

        $transformation = $manager->createData($resource)->toArray();

        return $transformation['data'];
    }
}
