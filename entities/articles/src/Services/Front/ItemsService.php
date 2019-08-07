<?php

namespace InetStudio\ArticlesPackage\Articles\Services\Front;

use InetStudio\AdminPanel\Base\Services\BaseService;
use InetStudio\AdminPanel\Base\Services\Traits\SlugsServiceTrait;
use InetStudio\Favorites\Services\Front\Traits\FavoritesServiceTrait;
use InetStudio\TagsPackage\Tags\Services\Front\Traits\TagsServiceTrait;
use InetStudio\ArticlesPackage\Articles\Contracts\Models\ArticleModelContract;
use InetStudio\ArticlesPackage\Articles\Contracts\Services\Front\ItemsServiceContract;
use InetStudio\CategoriesPackage\Categories\Services\Front\Traits\CategoriesServiceTrait;

/**
 * Class ItemsService.
 */
class ItemsService extends BaseService implements ItemsServiceContract
{
    use TagsServiceTrait;
    use SlugsServiceTrait;
    use FavoritesServiceTrait;
    use CategoriesServiceTrait;

    /**
     * @var string
     */
    protected $favoritesType = 'article';

    /**
     * ItemsService constructor.
     */
    public function __construct(ArticleModelContract $model)
    {
        parent::__construct($model);
    }
}
