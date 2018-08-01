<?php

namespace InetStudio\Articles\Transformers\Front;

use League\Fractal\TransformerAbstract;
use League\Fractal\Resource\Collection as FractalCollection;
use InetStudio\Articles\Contracts\Models\ArticleModelContract;
use InetStudio\Articles\Contracts\Transformers\Front\ArticlesMindboxFeedItemsTransformerContract;

/**
 * Class ArticlesMindboxFeedItemsTransformer.
 */
class ArticlesMindboxFeedItemsTransformer extends TransformerAbstract implements ArticlesMindboxFeedItemsTransformerContract
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
        $picture = '';

        try {
            $picture = asset($item->getFirstMediaUrl('preview', 'preview_3_2'));
        } catch (\Exception $e) {}

        return [
            'id' => $item->id,
            'available' => $item->status->classifiers->contains('alias', 'status_display_for_users') ? 'true' : 'false',
            'picture' => $picture,
            'name' => $item->title,
            'url' => $item->href,
            'description' => html_entity_decode(strip_tags($item->description)),
            'categories' => ($item->categories->count() > 0) ? $item->categories->pluck('id')->toArray() : [],
            'tags' => ($item->tags->count() > 0) ? implode('|', $item->tags->pluck('name')->toArray()) : '',
            'type' => 'Статья',
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
