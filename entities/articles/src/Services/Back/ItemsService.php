<?php

namespace InetStudio\ArticlesPackage\Articles\Services\Back;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use InetStudio\AdminPanel\Base\Services\BaseService;
use Illuminate\Contracts\Container\BindingResolutionException;
use InetStudio\ArticlesPackage\Articles\Contracts\Models\ArticleModelContract;
use InetStudio\ArticlesPackage\Articles\Contracts\Services\Back\ItemsServiceContract;

/**
 * Class ItemsService.
 */
class ItemsService extends BaseService implements ItemsServiceContract
{
    /**
     * ItemsService constructor.
     *
     * @param  ArticleModelContract  $model
     */
    public function __construct(ArticleModelContract $model)
    {
        parent::__construct($model);
    }

    /**
     * Сохраняем модель.
     *
     * @param  array  $data
     * @param  int  $id
     *
     * @return ArticleModelContract
     *
     * @throws BindingResolutionException
     */
    public function save(array $data, int $id): ArticleModelContract
    {
        $action = ($id) ? 'отредактирован' : 'создан';

        $itemData = Arr::only($data, $this->model->getFillable());
        $item = $this->saveModel($itemData, $id);

        $metaData = Arr::get($data, 'meta', []);
        app()->make('InetStudio\MetaPackage\Meta\Contracts\Services\Back\ItemsServiceContract')
            ->attachToObject($metaData, $item);

        $tagsData = Arr::get($data, 'tags', []);
        app()->make('InetStudio\TagsPackage\Tags\Contracts\Services\Back\ItemsServiceContract')
            ->attachToObject($tagsData, $item);

        $classifiersData = Arr::get($data, 'classifiers', []);
        app()->make('InetStudio\Classifiers\Entries\Contracts\Services\Back\ItemsServiceContract')
            ->attachToObject($classifiersData, $item);

        $categoriesData = Arr::get($data, 'categories', []);
        app()->make('InetStudio\CategoriesPackage\Categories\Contracts\Services\Back\ItemsServiceContract')
            ->attachToObject($categoriesData, $item);

        app()->make('InetStudio\Access\Contracts\Services\Back\AccessServiceContract')
            ->attachToObject(request(), $item);

        app()->make('InetStudio\Widgets\Contracts\Services\Back\WidgetsServiceContract')
            ->attachToObject(request(), $item);

        $images = (config('articles.images.conversions.'.$item->material_type)) ? array_keys(config('articles.images.conversions.'.$item->material_type)) : [];
        app()->make('InetStudio\Uploads\Contracts\Services\Back\ImagesServiceContract')
            ->attachToObject(request(), $item, $images, 'articles', $item->material_type);

        $item->searchable();

        event(
            app()->makeWith(
                'InetStudio\ArticlesPackage\Articles\Contracts\Events\Back\ModifyItemEventContract',
                compact('item')
            )
        );

        Session::flash('success', 'Статья «'.$item['title'].'» успешно '.$action);

        return $item;
    }

    /**
     * Возвращаем статистику объектов по статусу.
     *
     * @return mixed
     */
    public function getItemsStatisticByStatus()
    {
        $items = $this->model::buildQuery(
                [
                    'relations' => ['status'],
                ]
            )
            ->select(['status_id', DB::raw('count(*) as total')])
            ->groupBy('status_id')
            ->get();

        return $items;
    }
}
