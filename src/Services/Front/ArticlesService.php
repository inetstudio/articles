<?php

namespace InetStudio\Articles\Services\Front;

use League\Fractal\Manager;
use InetStudio\Articles\Models\ArticleModel;
use League\Fractal\Serializer\DataArraySerializer;
use InetStudio\Articles\Contracts\Services\Front\ArticlesServiceContract;
use InetStudio\Articles\Transformers\Front\ArticlesSiteMapTransformer;
use InetStudio\Articles\Transformers\Front\ArticlesFeedItemsTransformer;

/**
 * Class ArticlesService.
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
            $statusQuery->whereHas('classifiers', function ($classifiersQuery) {
                $classifiersQuery->where('classifiers.alias', 'status_display_for_users');
            });
        })->whereNotNull('publish_date')->orderBy('publish_date', 'desc')->limit(500)->get();

        $resource = (new ArticlesFeedItemsTransformer())->transformCollection($articles);

        return $this->serializeToArray($resource);
    }

    /**
     * Получаем информацию по статьям для карты сайта.
     *
     * @return array
     */
    public function getSiteMapItems(): array
    {
        $articles = ArticleModel::select(['slug', 'created_at', 'status_id', 'updated_at'])
            ->whereHas('status', function ($statusQuery) {
                $statusQuery->whereHas('classifiers', function ($classifiersQuery) {
                    $classifiersQuery->where('classifiers.alias', 'status_display_for_users');
                });
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $resource = (new ArticlesSiteMapTransformer())->transformCollection($articles);

        return $this->serializeToArray($resource);
    }

    /**
     * Преобразовываем данные в массив.
     *
     * @param $resource
     *
     * @return array
     */
    private function serializeToArray($resource): array
    {
        $manager = new Manager();
        $manager->setSerializer(new DataArraySerializer());

        $transformation = $manager->createData($resource)->toArray();

        return $transformation['data'];
    }
}
