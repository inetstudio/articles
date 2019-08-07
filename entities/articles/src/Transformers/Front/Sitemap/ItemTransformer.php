<?php

namespace InetStudio\ArticlesPackage\Articles\Transformers\Front\Sitemap;

use Throwable;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;
use League\Fractal\Resource\Collection as FractalCollection;
use InetStudio\ArticlesPackage\Articles\Contracts\Models\ArticleModelContract;
use InetStudio\ArticlesPackage\Articles\Contracts\Transformers\Front\Sitemap\ItemTransformerContract;

/**
 * Class ItemTransformer.
 */
class ItemTransformer extends TransformerAbstract implements ItemTransformerContract
{
    /**
     * Подготовка данных для отображения в карте сайта.
     *
     * @param  ArticleModelContract  $item
     *
     * @return array
     *
     * @throws Throwable
     */
    public function transform(ArticleModelContract $item): array
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

    /**
     * Обработка коллекции статей.
     *
     * @param $items
     *
     * @return FractalCollection
     */
    public function transformCollection($items): FractalCollection
    {
        return new FractalCollection($items, $this);
    }
}
