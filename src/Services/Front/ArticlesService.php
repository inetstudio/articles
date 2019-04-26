<?php

namespace InetStudio\Articles\Services\Front;

use League\Fractal\Manager;
use League\Fractal\Serializer\DataArraySerializer;
use InetStudio\AdminPanel\Services\Front\BaseService;
use InetStudio\TagsPackage\Tags\Services\Front\Traits\TagsServiceTrait;
use InetStudio\AdminPanel\Services\Front\Traits\SlugsServiceTrait;
use InetStudio\Favorites\Services\Front\Traits\FavoritesServiceTrait;
use InetStudio\Categories\Services\Front\Traits\CategoriesServiceTrait;
use InetStudio\Articles\Contracts\Services\Front\ArticlesServiceContract;

/**
 * Class ArticlesService.
 */
class ArticlesService extends BaseService implements ArticlesServiceContract
{
    use TagsServiceTrait;
    use SlugsServiceTrait;
    use FavoritesServiceTrait;
    use CategoriesServiceTrait;

    public $model;

    /**
     * ArticlesService constructor.
     */
    public function __construct()
    {
        parent::__construct(app()->make('InetStudio\Articles\Contracts\Repositories\ArticlesRepositoryContract'));
        $this->model = app()->make('InetStudio\Articles\Contracts\Models\ArticleModelContract');
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
     * Получаем информацию по статьям для карты сайта.
     *
     * @return array
     */
    public function getSiteMapItems(): array
    {
        $items = $this->repository->getAllItems([
            'columns' => ['created_at', 'updated_at'],
            'order' => ['created_at' => 'desc'],
        ]);

        $resource = app()->make('InetStudio\Articles\Contracts\Transformers\Front\ArticlesSiteMapTransformerContract')
            ->transformCollection($items);

        $manager = new Manager();
        $manager->setSerializer(new DataArraySerializer());

        $transformation = $manager->createData($resource)->toArray();

        return $transformation['data'];
    }
}
