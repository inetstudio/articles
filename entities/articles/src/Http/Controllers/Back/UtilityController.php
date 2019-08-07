<?php

namespace InetStudio\ArticlesPackage\Articles\Http\Controllers\Back;

use Illuminate\Http\Request;
use Cviebrock\EloquentSluggable\Services\SlugService;
use InetStudio\AdminPanel\Base\Http\Controllers\Controller;
use Illuminate\Contracts\Container\BindingResolutionException;
use InetStudio\ArticlesPackage\Articles\Contracts\Services\Back\ItemsServiceContract;
use InetStudio\ArticlesPackage\Articles\Contracts\Services\Back\UtilityServiceContract;
use InetStudio\ArticlesPackage\Articles\Contracts\Http\Controllers\Back\UtilityControllerContract;
use InetStudio\ArticlesPackage\Articles\Contracts\Http\Responses\Back\Utility\SlugResponseContract;
use InetStudio\ArticlesPackage\Articles\Contracts\Http\Responses\Back\Utility\SuggestionsResponseContract;

/**
 * Class UtilityController.
 */
class UtilityController extends Controller implements UtilityControllerContract
{
    /**
     * Получаем slug для модели по строке.
     *
     * @param  ItemsServiceContract  $itemsService
     * @param  Request  $request
     *
     * @return SlugResponseContract
     *
     * @throws BindingResolutionException
     */
    public function getSlug(ItemsServiceContract $itemsService, Request $request): SlugResponseContract
    {
        $id = (int) $request->get('id');
        $name = $request->get('name');

        $model = $itemsService->getItemById($id);
        $slug = ($name) ? SlugService::createSlug($model, 'slug', $name) : '';

        return $this->app->make(SlugResponseContract::class, compact('slug'));
    }

    /**
     * Возвращаем объекты для поля.
     *
     * @param  UtilityServiceContract  $utilityService
     * @param  Request  $request
     *
     * @return SuggestionsResponseContract
     *
     * @throws BindingResolutionException
     */
    public function getSuggestions(UtilityServiceContract $utilityService, Request $request): SuggestionsResponseContract
    {
        $search = $request->get('q', '') ?? '';
        $type = $request->get('type', '') ?? '';

        $items = $utilityService->getSuggestions($search);

        return $this->app->make(SuggestionsResponseContract::class, compact('items', 'type'));
    }
}
