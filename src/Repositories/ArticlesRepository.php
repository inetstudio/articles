<?php

namespace InetStudio\Articles\Repositories;

use InetStudio\AdminPanel\Repositories\BaseRepository;
use InetStudio\Articles\Contracts\Models\ArticleModelContract;
use InetStudio\AdminPanel\Repositories\Traits\SlugsRepositoryTrait;
use InetStudio\Favorites\Repositories\Traits\FavoritesRepositoryTrait;
use InetStudio\Articles\Contracts\Repositories\ArticlesRepositoryContract;

/**
 * Class ArticlesRepository.
 */
class ArticlesRepository extends BaseRepository implements ArticlesRepositoryContract
{
    use SlugsRepositoryTrait;
    use FavoritesRepositoryTrait;

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
                $query->select(['classifiers_entries.id', 'classifiers_entries.value', 'classifiers_entries.alias']);
            },

            'meta' => function ($query) {
                $query->select(['metable_id', 'metable_type', 'key', 'value']);
            },

            'media' => function ($query) {
                $query->select(['id', 'model_id', 'model_type', 'collection_name', 'file_name', 'disk', 'mime_type', 'custom_properties', 'responsive_images']);
            },

            'tags' => function ($query) {
                $query->select(['id', 'name', 'slug']);
            },

            'categories' => function ($query) {
                $query->select(['id', 'parent_id', 'name', 'slug', 'title', 'description']);
            },

            'counters' => function ($query) {
                $query->select(['countable_id', 'countable_type', 'type', 'counter']);
            },

            'status' => function ($query) {
                $query->select(['id', 'name', 'alias', 'color_class']);
            },
        ];
    }
}
