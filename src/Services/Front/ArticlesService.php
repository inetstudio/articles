<?php

namespace InetStudio\Articles\Services\Front;

use League\Fractal\Manager;
use League\Fractal\Serializer\DataArraySerializer;
use InetStudio\Articles\Contracts\Services\Front\ArticlesServiceContract;

/**
 * Class ArticlesService.
 */
class ArticlesService implements ArticlesServiceContract
{
    /**
     * @var
     */
    public $repository;

    /**
     * ArticlesService constructor.
     */
    public function __construct()
    {
        $this->repository = app()->make('InetStudio\Articles\Contracts\Repositories\ArticlesRepositoryContract');
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
     * Получаем объекты по id.
     *
     * @param $ids
     * @param array $extColumns
     * @param array $with
     * @param bool $returnBuilder
     *
     * @return mixed
     */
    public function getArticlesByIds($ids, array $extColumns = [], array $with = [], bool $returnBuilder = false)
    {
        return $this->repository->getItemsByIDs($ids, $extColumns, $with, $returnBuilder);
    }

    /**
     * Получаем объект по slug.
     *
     * @param string $slug
     * @param array $extColumns
     * @param array $with
     * @param bool $returnBuilder
     *
     * @return mixed
     */
    public function getArticleBySlug(string $slug, array $extColumns = [], array $with = [], bool $returnBuilder = false)
    {
        return $this->repository->getItemBySlug($slug, $extColumns, $with, $returnBuilder);
    }

    /**
     * Получаем все объекты.
     *
     * @param array $extColumns
     * @param array $with
     * @param bool $returnBuilder
     *
     * @return mixed
     */
    public function getAllArticles(array $extColumns = [], array $with = [], bool $returnBuilder = false)
    {
        return $this->repository->getAllItems($extColumns, $with, $returnBuilder);
    }

    /**
     * Получаем информацию по ингредиентам для фида.
     *
     * @return array
     */
    public function getFeedItems(): array
    {
        $items = $this->repository->getAllItems(['title', 'description', 'content', 'publish_date'], ['categories'], true)
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
