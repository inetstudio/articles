<?php

namespace InetStudio\Articles\Repositories;

use Illuminate\Database\Eloquent\Builder;
use InetStudio\Articles\Contracts\Models\ArticleModelContract;
use InetStudio\Articles\Contracts\Repositories\ArticlesRepositoryContract;
use InetStudio\Articles\Contracts\Http\Requests\Back\SaveArticleRequestContract;

/**
 * Class ArticlesRepository.
 */
class ArticlesRepository implements ArticlesRepositoryContract
{
    /**
     * @var ArticleModelContract
     */
    private $model;

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
     * @param bool $returnBuilder
     *
     * @return mixed
     */
    public function getItemsByIDs($ids, bool $returnBuilder = false)
    {
        $builder = $this->getItemsQuery()->whereIn('id', (array) $ids);

        if ($returnBuilder) {
            return $builder;
        }

        return $builder->get();
    }

    /**
     * Сохраняем объект.
     *
     * @param SaveArticleRequestContract $request
     * @param int $id
     *
     * @return ArticleModelContract
     */
    public function save(SaveArticleRequestContract $request, int $id): ArticleModelContract
    {
        $item = $this->getItemByID($id);

        $item->title = strip_tags($request->get('title'));
        $item->slug = strip_tags($request->get('slug'));
        $item->description = $request->input('description.text');
        $item->content = $request->input('content.text');
        $item->webmaster_id = '';
        $item->status_id = ($request->filled('status_id')) ? $request->get('status_id') : 1;
        $item->publish_date = ($request->filled('publish_date')) ? date('Y-m-d H:i', \DateTime::createFromFormat('!d.m.Y H:i', $request->get('publish_date'))->getTimestamp()) : null;
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
     * @param string $field
     * @param $value
     * @param bool $returnBuilder
     *
     * @return mixed
     */
    public function searchItemsByField(string $field, string $value, bool $returnBuilder = false)
    {
        $builder = $this->getItemsQuery(['title'])->where($field, 'LIKE', '%'.$value.'%');

        if ($returnBuilder) {
            return $builder;
        }

        return $builder->get();
    }

    /**
     * Получаем все объекты.
     *
     * @param bool $returnBuilder
     *
     * @return mixed
     */
    public function getAllItems(bool $returnBuilder = false)
    {
        $builder = $this->getItemsQuery(['created_at', 'updated_at']);

        if ($returnBuilder) {
            return $builder;
        }

        return $builder->get();
    }

    /**
     * Получаем объекты по slug.
     *
     * @param string $slug
     * @param bool $returnBuilder
     *
     * @return mixed
     */
    public function getItemBySlug(string $slug, bool $returnBuilder = false)
    {
        $builder = $this->getItemsQuery([
            'description', 'content', 'status_id', 'publish_date',
        ], [
            'meta', 'media', 'tags', 'categories', 'products', 'ingredients',
        ])->whereSlug($slug);

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
     * @param bool $returnBuilder
     *
     * @return mixed
     */
    public function getItemsFavoritedByUser(int $userID, bool $returnBuilder = false)
    {
        $builder = $this->getItemsQuery(['publish_date'], ['media', 'tags', 'categories', 'products', 'ingredients'])
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

            'ingredients' => function ($query) {
                $query->select(['id', 'title', 'slug', 'status_id'])
                    ->with(['media' => function ($query) {
                        $query->select(['id', 'model_id', 'model_type', 'collection_name', 'file_name', 'disk']);
                    }]);

                if ($this->role == 'user') {
                    $query->whereHas('status', function ($statusQuery) {
                        $statusQuery->whereHas('classifiers', function ($classifiersQuery) {
                            $classifiersQuery->where('classifiers.alias', 'status_display_for_users');
                        });
                    });
                }
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
