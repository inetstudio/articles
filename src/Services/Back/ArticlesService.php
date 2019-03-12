<?php

namespace InetStudio\Articles\Services\Back;

use League\Fractal\Manager;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use League\Fractal\Serializer\DataArraySerializer;
use InetStudio\AdminPanel\Base\Services\Back\BaseService;
use InetStudio\Articles\Contracts\Models\ArticleModelContract;
use InetStudio\Articles\Contracts\Services\Back\ArticlesServiceContract;
use InetStudio\Articles\Contracts\Http\Requests\Back\SaveArticleRequestContract;

/**
 * Class ArticlesService.
 */
class ArticlesService extends BaseService implements ArticlesServiceContract
{
    /**
     * Используемые сервисы.
     *
     * @var array
     */
    public $services = [];

    /**
     * @var
     */
    public $repository;

    /**
     * ArticlesService constructor.
     */
    public function __construct()
    {
        parent::__construct(app()->make('InetStudio\Articles\Contracts\Models\ArticleModelContract'));

        $this->services['meta'] = app()->make('InetStudio\Meta\Contracts\Services\Back\MetaServiceContract');
        $this->services['uploads'] = app()->make('InetStudio\Uploads\Contracts\Services\Back\ImagesServiceContract');
        $this->services['tags'] = app()->make('InetStudio\Tags\Contracts\Services\Back\TagsServiceContract');
        $this->services['classifiers'] = app()->make('InetStudio\Classifiers\Entries\Contracts\Services\Back\EntriesServiceContract');
        $this->services['categories'] = app()->make('InetStudio\Categories\Contracts\Services\Back\CategoriesServiceContract');
        $this->services['access'] = app()->make('InetStudio\Access\Contracts\Services\Back\AccessServiceContract');
        $this->services['widgets'] = app()->make('InetStudio\Widgets\Contracts\Services\Back\WidgetsServiceContract');

        $this->repository = app()->make('InetStudio\Articles\Contracts\Repositories\ArticlesRepositoryContract');
    }

    /**
     * Возвращаем объект модели.
     *
     * @param int $id
     *
     * @return ArticleModelContract
     */
    public function getArticleObject(int $id = 0)
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
     * Сохраняем модель.
     *
     * @param SaveArticleRequestContract $request
     * @param int $id
     *
     * @return ArticleModelContract
     */
    public function save(SaveArticleRequestContract $request, int $id): ArticleModelContract
    {
        $action = ($id) ? 'отредактирована' : 'создана';
        $item = $this->repository->save($request->only($this->repository->getModel()->getFillable()), $id);

        $this->services['meta']->attachToObject($request, $item);
        $this->services['tags']->attachToObject($request, $item);
        $this->services['classifiers']->attachToObject($request, $item);
        $this->services['categories']->attachToObject($request, $item);
        $this->services['access']->attachToObject($request, $item);
        $this->services['widgets']->attachToObject($request, $item);

        $images = (config('articles.images.conversions.'.$item->material_type)) ? array_keys(config('articles.images.conversions.'.$item->material_type)) : [];
        $this->services['uploads']->attachToObject($request, $item, $images, 'articles', $item->material_type);

        $item->searchable();

        event(app()->makeWith('InetStudio\Articles\Contracts\Events\Back\ModifyArticleEventContract', [
            'object' => $item,
        ]));

        Session::flash('success', 'Статья «'.$item->title.'» успешно '.$action);

        return $item;
    }

    /**
     * Возвращаем подсказки.
     *
     * @param string $search
     * @param $type
     *
     * @return array
     */
    public function getSuggestions(string $search, $type): array
    {
        $items = $this->repository->searchItems([['title', 'LIKE', '%'.$search.'%']]);

        $resource = (app()->makeWith('InetStudio\Articles\Contracts\Transformers\Back\SuggestionTransformerContract', [
            'type' => $type,
        ]))->transformCollection($items);

        $manager = new Manager();
        $manager->setSerializer(new DataArraySerializer());

        $transformation = $manager->createData($resource)->toArray();

        if ($type && $type == 'autocomplete') {
            $data['suggestions'] = $transformation['data'];
        } else {
            $data['items'] = $transformation['data'];
        }

        return $data;
    }

    /**
     * Возвращаем статистику статей по статусу.
     *
     * @return mixed
     */
    public function getArticlesStatisticByStatus()
    {
        $articles = $this->repository->getItemsQuery([
                'relations' => ['status'],
            ])
            ->select(['status_id', DB::raw('count(*) as total')])
            ->groupBy('status_id')
            ->get();

        return $articles;
    }
}
