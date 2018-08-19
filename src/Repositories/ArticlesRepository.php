<?php

namespace InetStudio\Articles\Repositories;

use Illuminate\Database\Eloquent\Builder;
use InetStudio\Tags\Repositories\Traits\TagsRepositoryTrait;
use InetStudio\Articles\Contracts\Models\ArticleModelContract;
use InetStudio\Favorites\Repositories\Traits\FavoritesRepositoryTrait;
use InetStudio\Categories\Repositories\Traits\CategoriesRepositoryTrait;
use InetStudio\Articles\Contracts\Repositories\ArticlesRepositoryContract;

/**
 * Class ArticlesRepository.
 */
class ArticlesRepository implements ArticlesRepositoryContract
{
    use TagsRepositoryTrait;
    use FavoritesRepositoryTrait;
    use CategoriesRepositoryTrait;

    /**
     * @var string
     */
    protected $favoritesType = 'article';

    /**
     * @var ArticleModelContract
     */
    public $model;

    /**
     * ArticlesRepository constructor.
     *
     * @param ArticleModelContract $model
     */
    public function __construct(ArticleModelContract $model)
    {
        $this->model = $model;
    }

    /**
     * Получаем модель репозитория.
     *
     * @return ArticleModelContract
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Возвращаем пустой объект по id.
     *
     * @param int $id
     *
     * @return mixed
     */
    public function getEmptyObjectById(int $id)
    {
        return $this->model::select(['id'])->where('id', '=', $id)->first();
    }

    /**
     * Возвращаем объект по id, либо создаем новый.
     *
     * @param int $id
     *
     * @return ArticleModelContract
     */
    public function getItemByID(int $id): ArticleModelContract
    {
        return $this->model::find($id) ?? new $this->model;
    }

    /**
     * Возвращаем удаленный объект по id, либо пустой.
     *
     * @param int $id
     *
     * @return ArticleModelContract
     */
    public function getTrashedItemByID(int $id = 0): ArticleModelContract
    {
        return $this->model::onlyTrashed()->find($id) ?? new $this->model;
    }

    /**
     * Возвращаем объекты по списку id.
     *
     * @param $ids
     * @param array $params
     *
     * @return mixed
     */
    public function getItemsByIDs($ids, array $params = [])
    {
        $builder = $this->getItemsQuery($params)
            ->whereIn('id', (array) $ids);

        return $builder->get();
    }

    /**
     * Сохраняем объект.
     *
     * @param array $data
     * @param int $id
     *
     * @return ArticleModelContract
     */
    public function save(array $data, int $id = 0): ArticleModelContract
    {
        $item = $this->getItemByID($id);
        $item->fill($data);
        $item->save();

        return $item;
    }

    /**
     * Удаляем объект.
     *
     * @param int $id
     *
     * @return bool
     */
    public function destroy(int $id = 0): ?bool
    {
        return $this->getItemByID($id)->delete();
    }

    /**
     * Ищем объекты.
     *
     * @param array $conditions
     * @param array $params
     *
     * @return mixed
     */
    public function searchItems(array $conditions, array $params = [])
    {
        $builder = $this->getItemsQuery($params)
            ->where($conditions);

        return $builder->get();
    }

    /**
     * Получаем все объекты.
     *
     * @param array $params
     *
     * @return mixed
     */
    public function getAllItems(array $params = [])
    {
        $builder = $this->getItemsQuery($params);

        return $builder->get();
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
        $builder = $this->getItemsQuery($params)->whereSlug($slug);

        $item = $builder->first();

        return $item;
    }

    /**
     * Возвращаем запрос на получение объектов.
     *
     * @param array $params
     *
     * @return Builder
     */
    public function getItemsQuery(array $params = []): Builder
    {
        $builder = $this->model::query();

        $defaultColumns = ['id', 'title', 'slug'];
        $relations = [
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

        if (isset($params['columns'])) {
            $builder->select(array_merge($defaultColumns, $params['columns']));
        }

        if (isset($params['relations'])) {
            $builder->with(array_intersect_key($relations, array_flip($params['relations'])));
        }

        if (isset($params['order'])) {
            foreach ($params['order'] as $column => $direction) {
                $builder->orderBy($column, $direction);
            }
        }

        if (isset($params['paging'])) {
            $skip = $params['paging']['page']*$params['paging']['limit'];

            $builder->skip($skip)->limit($params['paging']['limit']);
        }

        return $builder;
    }
}
