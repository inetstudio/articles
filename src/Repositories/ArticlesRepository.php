<?php

namespace InetStudio\Articles\Repositories;

use InetStudio\AdminPanel\Repositories\BaseRepository;
use InetStudio\Tags\Repositories\Traits\TagsRepositoryTrait;
use InetStudio\Articles\Contracts\Models\ArticleModelContract;
use InetStudio\Favorites\Repositories\Traits\FavoritesRepositoryTrait;
use InetStudio\Categories\Repositories\Traits\CategoriesRepositoryTrait;
use InetStudio\Articles\Contracts\Repositories\ArticlesRepositoryContract;

/**
 * Class ArticlesRepository.
 */
class ArticlesRepository extends BaseRepository implements ArticlesRepositoryContract
{
    use TagsRepositoryTrait;
    use FavoritesRepositoryTrait;
    use CategoriesRepositoryTrait;

    /**
     * @var string
     */
    protected $favoritesType = 'article';

    /**
     * ArticlesRepository constructor.
     *
     * @param ArticleModelContract $model
     */
    public function __construct(ArticleModelContract $model)
    {
        $this->model = $model;

        $this->defaultColumns = ['id', 'title', 'slug'];
        $this->relations = [
            'classifiers' => function ($query) {
                $query->select(['type', 'value', 'alias']);
            },

            'meta' => function ($query) {
                $query->select(['metable_id', 'metable_type', 'key', 'value']);
            },

            'media' => function ($query) {
                $query->select(['id', 'model_id', 'model_type', 'collection_name', 'file_name', 'disk', 'custom_properties']);
            },

            'tags' => function ($query) {
                $query->select(['id', 'name', 'slug']);
            },

            'categories' => function ($query) {
                $query->select(['id', 'parent_id', 'name', 'slug', 'title', 'description'])->whereNotNull('parent_id');
            },

            'counters' => function ($query) {
                $query->select(['countable_id', 'countable_type', 'type', 'counter']);
            },

            'status' => function ($query) {
                $query->select(['id', 'name', 'alias', 'color_class']);
            },
        ];
    }

    /**
     * Получаем объекты по slug.
     *
     * @param string $slug
     * @param array $params
     *
     * @return mixed
     */
    public function getItemBySlug(string $slug, array $params = [])
    {
        $builder = $this->getItemsQuery($params)
            ->whereSlug($slug);

        $item = $builder->first();

        return $item;
    }
}
