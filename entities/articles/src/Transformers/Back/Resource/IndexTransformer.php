<?php

namespace InetStudio\ArticlesPackage\Articles\Transformers\Back\Resource;

use Throwable;
use InetStudio\AdminPanel\Base\Transformers\BaseTransformer;
use InetStudio\ArticlesPackage\Articles\Contracts\Transformers\Back\Resource\IndexTransformerContract;

/**
 * Class IndexTransformer.
 */
class IndexTransformer extends BaseTransformer implements IndexTransformerContract
{
    /**
     * Подготовка данных для отображения в таблице.
     *
     * @param $item
     *
     * @return array
     *
     * @throws Throwable
     */
    public function transform($item): array
    {
        return [
            'id' => (int) $item['id'],
            'title' => $item['title'],
            'material_type' => view(
                'admin.module.articles::back.partials.datatables.material_type',
                [
                    'type' => $item['material_type'],
                ]
            )->render(),
            'status' => view(
                'admin.module.articles::back.partials.datatables.status',
                [
                    'status' => $item['status'],
                ]
            )->render(),
            'created_at' => (string) $item['created_at'],
            'updated_at' => (string) $item['updated_at'],
            'publish_date' => (string) $item['publish_date'],
            'actions' => view(
                'admin.module.articles::back.partials.datatables.actions',
                [
                    'id' => $item['id'],
                    'href' => $item['href'],
                ]
            )->render(),
        ];
    }
}
