<?php

namespace InetStudio\ArticlesPackage\Articles\Services\Front;

use InetStudio\AdminPanel\Base\Services\BaseService;
use InetStudio\ArticlesPackage\Articles\Contracts\Models\ArticleModelContract;
use InetStudio\ArticlesPackage\Articles\Contracts\Services\Front\FeedsServiceContract;

/**
 * Class FeedsService.
 */
class FeedsService extends BaseService implements FeedsServiceContract
{
    /**
     * FeedsService constructor.
     *
     * @param  ArticleModelContract  $model
     */
    public function __construct(ArticleModelContract $model)
    {
        parent::__construct($model);
    }
}
