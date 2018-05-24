<?php

namespace InetStudio\Articles\Services\Back;

use League\Fractal\Manager;
use Illuminate\Support\Facades\Session;
use League\Fractal\Serializer\DataArraySerializer;
use InetStudio\Articles\Contracts\Models\ArticleModelContract;
use InetStudio\Articles\Contracts\Services\Back\ArticlesServiceContract;
use InetStudio\Articles\Contracts\Repositories\ArticlesRepositoryContract;
use InetStudio\Articles\Contracts\Http\Requests\Back\SaveArticleRequestContract;

/**
 * Class ArticlesService.
 */
class ArticlesService implements ArticlesServiceContract
{
    /**
     * @var ArticlesRepositoryContract
     */
    private $repository;

    /**
     * ArticlesService constructor.
     *
     * @param ArticlesRepositoryContract $repository
     */
    public function __construct(ArticlesRepositoryContract $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Получаем объект модели.
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
     * @param bool $returnBuilder
     *
     * @return mixed
     */
    public function getArticlesByIDs($ids, bool $returnBuilder = false)
    {
        return $this->repository->getItemsByIDs($ids, $returnBuilder);
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
        $item = $this->repository->save($request, $id);

        app()->make('InetStudio\Meta\Contracts\Services\Back\MetaServiceContract')
            ->attachToObject($request, $item);

        $images = (config('articles.images.conversions.article')) ? array_keys(config('articles.images.conversions.article')) : [];
        app()->make('InetStudio\Uploads\Contracts\Services\Back\ImagesServiceContract')
            ->attachToObject($request, $item, $images, 'articles', 'article');

        app()->make('InetStudio\Tags\Contracts\Services\Back\TagsServiceContract')
            ->attachToObject($request, $item);

        app()->make('InetStudio\Products\Contracts\Services\Back\ProductsServiceContract')
            ->attachToObject($request, $item);

        app()->make('InetStudio\Classifiers\Contracts\Services\Back\ClassifiersServiceContract')
            ->attachToObject($request, $item);

        app()->make('InetStudio\Ingredients\Contracts\Services\Back\IngredientsServiceContract')
            ->attachToObject($request, $item);

        app()->make('InetStudio\Categories\Contracts\Services\Back\CategoriesServiceContract')
            ->attachToObject($request, $item);

        app()->make('InetStudio\Access\Contracts\Services\Back\AccessServiceContract')
            ->attachToObject($request, $item);

        app()->make('InetStudio\Widgets\Contracts\Services\Back\WidgetsServiceContract')
            ->attachToObject($request, $item);

        $item->searchable();

        event(app()->makeWith('InetStudio\Articles\Contracts\Events\Back\ModifyArticleEventContract', [
            'object' => $item,
        ]));

        Session::flash('success', 'Статья «'.$item->title.'» успешно '.$action);

        return $item;
    }

    /**
     * Удаляем модель.
     *
     * @param $id
     *
     * @return bool
     */
    public function destroy(int $id): ?bool
    {
        return $this->repository->destroy($id);
    }

    /**
     * Получаем подсказки.
     *
     * @param string $search
     * @param $type
     *
     * @return array
     */
    public function getSuggestions(string $search, $type): array
    {
        $items = $this->repository->searchItemsByField('title', $search);

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
}
