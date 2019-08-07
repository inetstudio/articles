<?php

namespace InetStudio\ArticlesPackage\Articles\Transformers\Front\Sitemap;

use Throwable;
use Carbon\Carbon;
use InetStudio\AdminPanel\Base\Transformers\BaseTransformer;
use InetStudio\ArticlesPackage\Articles\Contracts\Transformers\Front\Sitemap\ItemTransformerContract;

/**
 * Class ItemTransformer.
 */
class ItemTransformer extends BaseTransformer implements ItemTransformerContract
{
    /**
     * Подготовка данных для отображения в карте сайта.
     *
     * @param $item
     *
     * @return array
     *
     * @throws Throwable
     */
    public function transform($item): array
    {
        /** @var Carbon $updatedAt */
        $updatedAt = $item['updated_at'];

        return [
            'loc' => $item['href'],
            'lastmod' => $updatedAt->toW3cString(),
            'priority' => '1.0',
            'freq' => 'daily',
        ];
    }
}
