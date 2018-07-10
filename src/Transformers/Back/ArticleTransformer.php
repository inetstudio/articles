<?php

namespace InetStudio\Articles\Transformers\Back;

use League\Fractal\TransformerAbstract;
use InetStudio\Articles\Contracts\Models\ArticleModelContract;
use InetStudio\Articles\Contracts\Transformers\Back\ArticleTransformerContract;

/**
 * Class ArticleTransformer.
 */
class ArticleTransformer extends TransformerAbstract implements ArticleTransformerContract
{
    /**
     * Подготовка данных для отображения в таблице.
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
            'id' => (int) $item->id,
            'title' => $item->title,
            'material_type' => view('admin.module.articles::back.partials.datatables.material_type', [
                'type' => $item->material_type,
            ])->render(),
            'status' => view('admin.module.articles::back.partials.datatables.status', [
                'status' => $item->status,
            ])->render(),
            'created_at' => (string) $item->created_at,
            'updated_at' => (string) $item->updated_at,
            'publish_date' => (string) $item->publish_date,
            'actions' => view('admin.module.articles::back.partials.datatables.actions', [
                'id' => $item->id,
                'href' => $item->href,
            ])->render(),
        ];
    }
}
