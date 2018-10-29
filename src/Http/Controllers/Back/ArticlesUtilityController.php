<?php

namespace InetStudio\Articles\Http\Controllers\Back;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Cviebrock\EloquentSluggable\Services\SlugService;
use InetStudio\Articles\Contracts\Http\Responses\Back\Utility\SlugResponseContract;
use InetStudio\Articles\Contracts\Http\Responses\Back\Utility\SuggestionsResponseContract;
use InetStudio\Articles\Contracts\Http\Controllers\Back\ArticlesUtilityControllerContract;

/**
 * Class ArticlesUtilityController.
 */
class ArticlesUtilityController extends Controller implements ArticlesUtilityControllerContract
{
    /**
     * Используемые сервисы.
     *
     * @var array
     */
    public $services;

    /**
     * ArticlesUtilityController constructor.
     */
    public function __construct()
    {
        $this->services['articles'] = app()->make('InetStudio\Articles\Contracts\Services\Back\ArticlesServiceContract');
    }

    /**
     * Получаем slug для модели по строке.
     *
     * @param Request $request
     *
     * @return SlugResponseContract
     */
    public function getSlug(Request $request): SlugResponseContract
    {
        $id = (int) $request->get('id');
        $name = $request->get('name');

        $model = $this->services['articles']->getArticleObject($id);

        $slug = ($name) ? SlugService::createSlug($model, 'slug', $name) : '';

        return app()->makeWith('InetStudio\Articles\Contracts\Http\Responses\Back\Utility\SlugResponseContract', [
            'slug' => $slug,
        ]);
    }

    /**
     * Возвращаем статьи для поля.
     *
     * @param Request $request
     *
     * @return SuggestionsResponseContract
     */
    public function getSuggestions(Request $request): SuggestionsResponseContract
    {
        $search = $request->get('q');
        $type = $request->get('type');

        $data = $this->services['articles']->getSuggestions($search, $type);

        return app()->makeWith('InetStudio\Articles\Contracts\Http\Responses\Back\Utility\SuggestionsResponseContract', [
            'suggestions' => $data,
        ]);
    }
}
