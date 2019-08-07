<?php

namespace InetStudio\ArticlesPackage\Articles\Contracts\Services\Front;

use InetStudio\AdminPanel\Base\Contracts\Services\BaseServiceContract;

/**
 * Interface SitemapServiceContract.
 */
interface SitemapServiceContract extends BaseServiceContract
{
    /**
     * Получаем информацию по объектам для карты сайта.
     *
     * @param  array $params
     *
     * @return array
     */
    public function getItems(array $params = []): array;
}
