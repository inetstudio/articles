<?php

namespace InetStudio\Articles\Transformers\Front;

use League\Fractal\TransformerAbstract;
use InetStudio\Articles\Models\ArticleModel;
use League\Fractal\Resource\Collection as FractalCollection;

/**
 * Class ArticlesSiteMapTransformer
 * @package InetStudio\Articles\Transformers\Front
 */
class ArticlesSiteMapTransformer extends TransformerAbstract
{
    /**
     * Подготовка данных для отображения в карте сайта.
     *
     * @param ArticleModel $article
     *
     * @return array
     *
     * @throws \Throwable
     */
    public function transform(ArticleModel $article): array
    {
        return [
            'loc' => $article->href,
            'lastmod' => $article->updated_at->toW3cString(),
            'priority' => '1.0',
            'freq' => 'daily',
        ];
    }

    /**
     * Обработка коллекции статей.
     *
     * @param $articles
     *
     * @return FractalCollection
     */
    public function transformCollection($articles): FractalCollection
    {
        return new FractalCollection($articles, $this);
    }
}
