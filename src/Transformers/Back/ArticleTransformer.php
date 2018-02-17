<?php

namespace InetStudio\Articles\Transformers\Back;

use League\Fractal\TransformerAbstract;
use InetStudio\Articles\Models\ArticleModel;

/**
 * Class ArticleTransformer.
 */
class ArticleTransformer extends TransformerAbstract
{
    /**
     * Подготовка данных для отображения в таблице.
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
            'id' => (int) $article->id,
            'title' => $article->title,
            'status' => view('admin.module.articles::back.partials.datatables.status', [
                'status' => $article->status,
            ])->render(),
            'created_at' => (string) $article->created_at,
            'updated_at' => (string) $article->updated_at,
            'publish_date' => (string) $article->publish_date,
            'actions' => view('admin.module.articles::back.partials.datatables.actions', [
                'id' => $article->id,
                'href' => $article->href,
            ])->render(),
        ];
    }
}
