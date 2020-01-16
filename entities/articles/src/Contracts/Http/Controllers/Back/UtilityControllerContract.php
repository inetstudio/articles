<?php

namespace InetStudio\ArticlesPackage\Articles\Contracts\Http\Controllers\Back;

use Illuminate\Http\Request;
use InetStudio\ArticlesPackage\Articles\Contracts\Services\Back\ItemsServiceContract;
use InetStudio\ArticlesPackage\Articles\Contracts\Services\Back\UtilityServiceContract;
use InetStudio\ArticlesPackage\Articles\Contracts\Http\Responses\Back\Utility\SlugResponseContract;
use InetStudio\ArticlesPackage\Articles\Contracts\Http\Responses\Back\Utility\SuggestionsResponseContract;

/**
 * Interface UtilityControllerContract.
 */
interface UtilityControllerContract
{
    /**
     * Получаем slug для модели по строке.
     *
     * @param  ItemsServiceContract  $itemsService
     * @param  Request  $request
     * @param  string  $type
     *
     * @return SlugResponseContract
     */
    public function getSlug(ItemsServiceContract $itemsService, Request $request, string $type): SlugResponseContract;

    /**
     * Возвращаем объекты для поля.
     *
     * @param  UtilityServiceContract  $utilityService
     * @param  Request  $request
     *
     * @return SuggestionsResponseContract
     */
    public function getSuggestions(UtilityServiceContract $utilityService, Request $request): SuggestionsResponseContract;
}
