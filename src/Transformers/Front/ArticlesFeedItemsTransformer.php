<?php

namespace InetStudio\Articles\Transformers\Front;

use League\Fractal\TransformerAbstract;
use InetStudio\Articles\Models\ArticleModel;
use League\Fractal\Resource\Collection as FractalCollection;

/**
 * Class ArticlesFeedItemsTransformer
 * @package InetStudio\Articles\Transformers\Front
 */
class ArticlesFeedItemsTransformer extends TransformerAbstract
{
    /**
     * Подготовка данных для отображения в фиде.
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
            'title' => $article->title,
            'author' => $this->getAuthor($article),
            'link' => $article->href,
            'pubdate' => $article->publish_date,
            'description' => $article->description,
            'content' => $article->content,
            'enclosure' => [],
            'category' => ($article->categories->count() > 0) ? $article->categories->first()->title : '',
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

    /**
     * Получаем автора статьи.
     *
     * @param ArticleModel $article
     *
     * @return string
     */
    private function getAuthor(ArticleModel $article): string
    {
        foreach ($article->revisionHistory as $history) {
            if ($history->key == 'created_at' && ! $history->old_value) {
                return $history->userResponsible()->name;
            }
        }

        return '';
    }
}
