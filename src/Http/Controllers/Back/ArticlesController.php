<?php

namespace InetStudio\Articles\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use InetStudio\Articles\Contracts\Http\Requests\Back\SaveArticleRequestContract;
use InetStudio\Articles\Contracts\Http\Controllers\Back\ArticlesControllerContract;
use InetStudio\Articles\Contracts\Http\Responses\Back\Articles\FormResponseContract;
use InetStudio\Articles\Contracts\Http\Responses\Back\Articles\SaveResponseContract;
use InetStudio\Articles\Contracts\Http\Responses\Back\Articles\ShowResponseContract;
use InetStudio\Articles\Contracts\Http\Responses\Back\Articles\IndexResponseContract;
use InetStudio\Articles\Contracts\Http\Responses\Back\Articles\DestroyResponseContract;

/**
 * Class ArticlesController.
 */
class ArticlesController extends Controller implements ArticlesControllerContract
{
    /**
     * Используемые сервисы.
     *
     * @var array
     */
    public $services;

    /**
     * ArticlesController constructor.
     */
    public function __construct()
    {
        $this->services['articles'] = app()->make('InetStudio\Articles\Contracts\Services\Back\ArticlesServiceContract');
        $this->services['dataTables'] = app()->make('InetStudio\Articles\Contracts\Services\Back\ArticlesDataTableServiceContract');
    }

    /**
     * Список объектов.
     *
     * @return IndexResponseContract
     */
    public function index(): IndexResponseContract
    {
        $table = $this->services['dataTables']->html();

        return app()->makeWith('InetStudio\Articles\Contracts\Http\Responses\Back\Articles\IndexResponseContract', [
            'data' => compact('table'),
        ]);
    }

    /**
     * Получение объекта.
     *
     * @param int $id
     *
     * @return ShowResponseContract
     */
    public function show(int $id = 0): ShowResponseContract
    {
        $item = $this->services['articles']->getArticleObject($id);

        return app()->makeWith(ShowResponseContract::class, [
            'item' => $item,
        ]);
    }

    /**
     * Создание объекта.
     *
     * @param string $type
     *
     * @return FormResponseContract
     */
    public function create(string $type = ''): FormResponseContract
    {
        $item = $this->services['articles']->getArticleObject();

        $item->material_type = $type;

        return app()->makeWith(FormResponseContract::class, [
            'data' => compact('item'),
        ]);
    }

    /**
     * Создание объекта.
     *
     * @param SaveArticleRequestContract $request
     *
     * @return SaveResponseContract
     */
    public function store(SaveArticleRequestContract $request): SaveResponseContract
    {
        return $this->save($request);
    }

    /**
     * Редактирование объекта.
     *
     * @param int $id
     *
     * @return FormResponseContract
     */
    public function edit(int $id = 0): FormResponseContract
    {
        $item = $this->services['articles']->getArticleObject($id);

        return app()->makeWith(FormResponseContract::class, [
            'data' => compact('item'),
        ]);
    }

    /**
     * Обновление объекта.
     *
     * @param SaveArticleRequestContract $request
     * @param int $id
     *
     * @return SaveResponseContract
     */
    public function update(SaveArticleRequestContract $request, int $id = 0): SaveResponseContract
    {
        return $this->save($request, $id);
    }

    /**
     * Сохранение объекта.
     *
     * @param SaveArticleRequestContract $request
     * @param int $id
     *
     * @return SaveResponseContract
     */
    private function save(SaveArticleRequestContract $request, int $id = 0): SaveResponseContract
    {
        $item = $this->services['articles']->save($request, $id);

        return app()->makeWith(SaveResponseContract::class, [
            'item' => $item,
        ]);
    }

    /**
     * Удаление объекта.
     *
     * @param int $id
     *
     * @return DestroyResponseContract
     */
    public function destroy(int $id = 0): DestroyResponseContract
    {
        $result = $this->services['articles']->destroy($id);

        return app()->makeWith(DestroyResponseContract::class, [
            'result' => (!! $result),
        ]);
    }
}
