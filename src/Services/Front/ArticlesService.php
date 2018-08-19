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
     * @param array $params
     *
     * @return mixed
     */
    public function getArticlesByIDs($ids, array $params = [])
    {
        return $this->repository->getItemsByIDs($ids, $params);
    }

    /**
     * Получаем объект по slug.
     *
     * @param string $slug
     * @param array $params
     *
     * @return mixed
     */
    public function getArticleBySlug(string $slug, array $params = [])
    {
        return $this->repository->getItemBySlug($slug, $params);
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
     * @param array $params
     *
     * @return mixed
     */
    public function getArticlesByCategory(string $categorySlug, array $params = [])
    {
        return $this->repository->getItemsByCategory($categorySlug, $params);
    }

    /**
     * Получаем объекты из категорий.
     *
     * @param $categories
     * @param array $params
     *
     * @return mixed
     */
    public function getArticlesFromCategories($categories, array $params = [])
    {
        return $this->repository->getItemsFromCategories($categories, $params);
    }

    /**
     * Получаем объекты из любых категорий.
     *
     * @param $categories
     * @param array $params
     *
     * @return mixed
     */
    public function getArticlesByAnyCategory($categories, array $params = [])
    {
        return $this->repository->getItemsByAnyCategory($categories, $params);
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
     * @param array $params
     *
     * @return mixed
     */
    public function getAllArticles(array $params = [])
    {
        return $this->repository->getAllItems($params);
    }

    /**
     * Получаем информацию по статьям для фида.
     *
     * @return array
     */
    public function getFeedItems(): array
    {
        $items = $this->repository->getItemsQuery([
                'columns' => ['title', 'description', 'content', 'publish_date'],
                'relations' => ['categories'],
                'order' => ['publish_date' => 'desc'],
                'paging' => [
                    'page' => 0,
                    'limit' => 500,
                ],
            ])
            ->whereNotNull('publish_date')
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
        $items = $this->repository->getAllItems([
            'columns' => ['title', 'description', 'status_id'],
            'relations' => ['media', 'categories', 'tags'],
        ]);

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
