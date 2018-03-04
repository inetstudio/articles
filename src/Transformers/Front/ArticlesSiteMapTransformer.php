<?php

namespace InetStudio\Articles\Transformers\Front;

use League\Fractal\TransformerAbstract;
use League\Fractal\Resource\Collection as FractalCollection;
use InetStudio\Articles\Contracts\Models\ArticleModelContract;
use InetStudio\Articles\Contracts\Transformers\Front\ArticlesSiteMapTransformerContract;

/**
 * Class ArticlesSiteMapTransformer.
 */
class ArticlesSiteMapTransformer extends TransformerAbstract implements ArticlesSiteMapTransformerContract
{
    /**
     * Подготовка данных для отображения в карте сайта.
     *
     * @param ArticleModelContract $item
     *
     * @return array
     *
     * @throws \Throwable
     */
    public function transform(ArticleModelContract $item): array
    {
        return [
            'loc' => $item->href,
            'lastmod' => $item->updated_at->toW3cString(),
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
