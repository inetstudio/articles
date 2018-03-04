<?php

namespace InetStudio\Articles\Transformers\Front;

use League\Fractal\TransformerAbstract;
use League\Fractal\Resource\Collection as FractalCollection;
use InetStudio\Articles\Contracts\Models\ArticleModelContract;
use InetStudio\Articles\Contracts\Transformers\Front\ArticlesFeedItemsTransformerContract;

/**
 * Class ArticlesFeedItemsTransformer.
 */
class ArticlesFeedItemsTransformer extends TransformerAbstract implements ArticlesFeedItemsTransformerContract
{
    /**
     * Подготовка данных для отображения в фиде.
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
            'title' => $item->title,
            'author' => $this->getAuthor($item),
            'link' => $item->href,
            'pubdate' => $item->publish_date,
            'description' => $item->description,
            'content' => $item->content,
            'enclosure' => [],
            'category' => ($item->categories->count() > 0) ? $item->categories->first()->title : '',
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

    /**
     * Получаем автора статьи.
     *
     * @param ArticleModelContract $item
     *
     * @return string
     */
    private function getAuthor(ArticleModelContract $item): string
    {
        foreach ($item->revisionHistory as $history) {
            if ($history->key == 'created_at' && ! $history->old_value) {
                $user = $history->userResponsible();

                return ($user) ? $user->name : '';
            }
        }

        return '';
    }
}
