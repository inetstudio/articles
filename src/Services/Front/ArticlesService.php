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
     * Получаем объекты по списку id.
     *
     * @param array|int $ids
     * @param array $properties
     * @param array $with
     * @param array $sort
     *
     * @return mixed
     */
    public function getArticlesByIDs($ids, array $properties = [], array $with = [], array $sort = [])
    {
        return $this->repository->getItemsByIDs($ids, $properties, $with, $sort);
    }

    /**
     * Получаем объект по slug.
     *
     * @param string $slug
     * @param array $properties
     * @param array $with
     *
     * @return mixed
     */
    public function getArticleBySlug(string $slug, array $properties = [], array $with = [])
    {
        return $this->repository->getItemBySlug($slug, $properties, $with);
    }

    /**
     * Получаем объекты по тегу.
     *
     * @param string $tagSlug
     * @param array $extColumns
     * @param array $with
     * @param bool $returnBuilder
     *
     * @return mixed
     */
    public function getArticlesByTag(string $tagSlug, array $extColumns = [], array $with = [], bool $returnBuilder = false)
    {
        return $this->repository->getItemsByTag($tagSlug, $extColumns, $with, $returnBuilder);
    }

    /**
     * Получаем объекты по категории.
     *
     * @param string $categorySlug
     * @param array $properties
     * @param array $with
     * @param array $sort
     *
     * @return mixed
     */
    public function getArticlesByCategory(string $categorySlug, array $properties = [], array $with = [], array $sort = [])
    {
        return $this->repository->getItemsByCategory($categorySlug, $properties, $with, $sort);
    }

    /**
     * Получаем объекты из категорий.
     *
     * @param $categories
     * @param array $properties
     * @param array $with
     * @param array $sort
     *
     * @return mixed
     */
    public function getArticlesFromCategories($categories, array $properties = [], array $with = [], array $sort = [])
    {
        return $this->repository->getItemsFromCategories($categories, $properties, $with, $sort);
    }

    /**
     * Получаем объекты из любых категорий.
     *
     * @param $categories
     * @param array $properties
     * @param array $with
     * @param array $sort
     *
     * @return mixed
     */
    public function getArticlesByAnyCategory($categories, array $properties = [], array $with = [], array $sort = [])
    {
        return $this->repository->getItemsByAnyCategory($categories, $properties, $with, $sort);
    }

    /**
     * Получаем сохраненные объекты пользователя.
     *
     * @param int $userID
     * @param array $extColumns
     * @param array $with
     * @param bool $returnBuilder
     *
     * @return mixed
     */
    public function getArticlesFavoritedByUser(int $userID, array $extColumns = [], array $with = [], bool $returnBuilder = false)
    {
        return $this->repository->getItemsFavoritedByUser($userID, $extColumns, $with, $returnBuilder);
    }

    /**
     * Получаем все объекты.
     *
     * @param array $properties
     * @param array $with
     * @param array $sort
     *
     * @return mixed
     */
    public function getAllArticles(array $properties = [], array $with = [], array $sort = [])
    {
        return $this->repository->getAllItems($properties, $with, $sort);
    }

    /**
     * Получаем информацию по статьям для фида.
     *
     * @return array
     */
    public function getFeedItems(): array
    {
        $items = $this->repository->getItemsQuery(['title', 'description', 'content', 'publish_date'], ['categories'])
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
     * Получаем информацию по статьям для фида mindbox.
     *
     * @return mixed
     */
    public function getMindboxFeedItems()
    {
        $items = $this->repository->getAllItems(['title', 'description', 'status_id'], ['media', 'categories', 'tags']);

        $resource = app()->make('InetStudio\Articles\Contracts\Transformers\Front\Feeds\Mindbox\ArticleTransformerContract')
            ->transformCollection($items);

        $manager = new Manager();
        $manager->setSerializer(new DataArraySerializer());

        $transformation = $manager->createData($resource)->toArray();

        return $transformation['data'];
    }

    /**
     * Получаем информацию по статьям для карты сайта.
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
