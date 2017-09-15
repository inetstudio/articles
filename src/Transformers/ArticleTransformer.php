<?php

namespace Inetstudio\Articles\Transformers;

use League\Fractal\TransformerAbstract;
use InetStudio\Articles\Models\ArticleModel;

class ArticleTransformer extends TransformerAbstract
{
    /**
     * @param ArticleModel $article
     * @return array
     */
    public function transform(ArticleModel $article)
    {
        return [
            'id' => (int) $article->id,
            'title' => $article->title,
            'status' => view('admin.module.ingredients::partials.datatables.status', [
                'status' => $article->status,
            ])->render(),
            'created_at' => (string) $article->created_at,
            'updated_at' => (string) $article->updated_at,
            'actions' => view('admin.module.articles::partials.datatables.actions', [
                'id' => $article->id,
                'href' => $article->href,
            ])->render(),
        ];
    }
}
