<?php

namespace InetStudio\Articles\Services\Front;

use League\Fractal\Manager;
use InetStudio\Articles\Models\ArticleModel;
use League\Fractal\Serializer\DataArraySerializer;
use InetStudio\Articles\Contracts\Services\ArticlesServiceContract;
use InetStudio\Articles\Transformers\Front\ArticlesFeedItemsTransformer;

/**
 * Class ArticlesService
 * @package InetStudio\Articles\Services\Front
 */
class ArticlesService implements ArticlesServiceContract
{
    /**
     * Получаем информацию по статьям для фида.
     *
     * @return array
     */
    public function getFeedItems(): array
    {
        $articles = ArticleModel::with('categories')->whereHas('status', function ($statusQuery) {
            $statusQuery->whereIn('alias', ['seo_check', 'published']);
        })->whereNotNull('publish_date')->orderBy('publish_date', 'desc')->limit(500)->get();

        $manager = new Manager();
        $manager->setSerializer(new DataArraySerializer());
        $resource = (new ArticlesFeedItemsTransformer())->transformCollection($articles);

        $transformation = $manager->createData($resource)->toArray();

        return $transformation['data'];
    }
}
