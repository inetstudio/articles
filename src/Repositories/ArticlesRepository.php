<?php

namespace InetStudio\Articles\Repositories;

use Illuminate\Database\Eloquent\Builder;
use InetStudio\Articles\Contracts\Models\ArticleModelContract;
use InetStudio\Categories\Repositories\Traits\CategoriesRepositoryTrait;
use InetStudio\Articles\Contracts\Repositories\ArticlesRepositoryContract;

/**
 * Class ArticlesRepository.
 */
class ArticlesRepository implements ArticlesRepositoryContract
{
    use CategoriesRepositoryTrait;

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
     * Возвращаем объекты по списку id.
     *
     * @param $ids
     * @param array $extColumns
     * @param array $with
     * @param bool $returnBuilder
     *
     * @return mixed
     */
    public function getItemsByIDs($ids, array $extColumns = [], array $with = [], bool $returnBuilder = false)
    {
        $builder = $this->getItemsQuery($extColumns, $with)->whereIn('id', (array) $ids);

        if ($returnBuilder) {
            return $builder;
        }

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
    public function save(array $data, int $id): ArticleModelContract
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
    public function destroy($id): ?bool
    {
        return $this->getItemByID($id)->delete();
    }

    /**
     * Ищем объекты.
     *
     * @param array $conditions
     * @param array $extColumns
     * @param array $with
     * @param bool $returnBuilder
     *
     * @return mixed
     */
    public function searchItems(array $conditions, array $extColumns = [], array $with = [], bool $returnBuilder = false)
    {
        $builder = $this->getItemsQuery($extColumns, $with)->where($conditions);

        if ($returnBuilder) {
            return $builder;
        }

        return $builder->get();
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
    public function getAllItems(array $extColumns = [], array $with = [], bool $returnBuilder = false)
    {
        $builder = $this->getItemsQuery(array_merge($extColumns, ['created_at', 'updated_at']), $with);

        if ($returnBuilder) {
            return $builder;
        }

        return $builder->get();
    }

    /**
     * Получаем объекты по slug.
     *
     * @param string $slug
     * @param array $extColumns
     * @param array $with
     * @param bool $returnBuilder
     *
     * @return mixed
     */
    public function getItemBySlug(string $slug, array $extColumns = [], array $with = [], bool $returnBuilder = false)
    {
        $builder = $this->getItemsQuery($extColumns, $with)->whereSlug($slug);

        if ($returnBuilder) {
            return $builder;
        }

        $item = $builder->first();

        return $item;
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
    public function getItemsFavoritedByUser(int $userID, array $extColumns = [], array $with = [], bool $returnBuilder = false)
    {
        $builder = $this->getItemsQuery(array_merge($extColumns, ['publish_date']), $with)
            ->orderBy('publish_date', 'DESC')
            ->whereFavoritedBy('article', $userID);

        if ($returnBuilder) {
            return $builder;
        }

        $items = $builder->get();

        return $items;
    }

    /**
     * Возвращаем запрос на получение объектов.
     *
     * @param array $extColumns
     * @param array $with
     *
     * @return Builder
     */
    protected function getItemsQuery($extColumns = [], $with = []): Builder
    {
        $defaultColumns = ['id', 'title', 'slug'];

        $relations = [
            'meta' => function ($query) {
                $query->select(['metable_id', 'metable_type', 'key', 'value']);
            },

            'media' => function ($query) {
                $query->select(['id', 'model_id', 'model_type', 'collection_name', 'file_name', 'disk']);
            },

            'tags' => function ($query) {
                $query->select(['id', 'name', 'slug']);
            },

            'categories' => function ($query) {
                $query->select(['id', 'parent_id', 'name', 'slug', 'title', 'description'])->whereNotNull('parent_id');
            },

            'products' => function ($query) {
                $query->select(['id', 'title', 'brand'])
                    ->with(['media' => function ($query) {
                        $query->select(['id', 'model_id', 'model_type', 'collection_name', 'file_name', 'disk']);
                    }, 'links' => function ($linksQuery) {
                        $linksQuery->select(['id', 'product_id', 'link']);
                    }]);
            },

            'counters' => function ($query) {
                $query->select(['countable_id', 'countable_type', 'type', 'counter']);
            },

            'status' => function ($query) {
                $query->select(['id', 'name', 'alias']);
            },
        ];

        return $this->model::select(array_merge($defaultColumns, $extColumns))
            ->with(array_intersect_key($relations, array_flip($with)));
    }
}
