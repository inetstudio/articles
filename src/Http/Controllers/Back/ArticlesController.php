<?php

namespace InetStudio\Articles\Http\Controllers\Back;

use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;
use InetStudio\Articles\Models\ArticleModel;
use InetStudio\Articles\Events\ModifyArticleEvent;
use InetStudio\AdminPanel\Http\Controllers\Back\Traits\DatatablesTrait;
use InetStudio\Meta\Http\Controllers\Back\Traits\MetaManipulationsTrait;
use InetStudio\Tags\Http\Controllers\Back\Traits\TagsManipulationsTrait;
use InetStudio\Access\Http\Controllers\Back\Traits\AccessManipulationsTrait;
use InetStudio\AdminPanel\Http\Controllers\Back\Traits\ImagesManipulationsTrait;
use InetStudio\Articles\Contracts\Http\Requests\Back\SaveArticleRequestContract;
use InetStudio\Products\Http\Controllers\Back\Traits\ProductsManipulationsTrait;
use InetStudio\Articles\Contracts\Http\Controllers\Back\ArticlesControllerContract;
use InetStudio\Categories\Http\Controllers\Back\Traits\CategoriesManipulationsTrait;
use InetStudio\Classifiers\Http\Controllers\Back\Traits\ClassifiersManipulationsTrait;
use InetStudio\Ingredients\Http\Controllers\Back\Traits\IngredientsManipulationsTrait;

/**
 * Class ArticlesController.
 */
class ArticlesController extends Controller implements ArticlesControllerContract
{
    use DatatablesTrait;
    use MetaManipulationsTrait;
    use TagsManipulationsTrait;
    use AccessManipulationsTrait;
    use ImagesManipulationsTrait;
    use ProductsManipulationsTrait;
    use CategoriesManipulationsTrait;
    use ClassifiersManipulationsTrait;
    use IngredientsManipulationsTrait;

    /**
     * Список статей.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     *
     * @throws \Exception
     */
    public function index(): View
    {
        $table = $this->generateTable('articles', 'index');

        return view('admin.module.articles::back.pages.index', compact('table'));
    }

    /**
     * Добавление статьи.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     *
     * @throws \Exception
     */
    public function create(): View
    {
        $table = $this->generateTable('products', 'embedded');

        return view('admin.module.articles::back.pages.form', [
            'item' => new ArticleModel(),
            'productsTable' => $table,
        ]);
    }

    /**
     * Создание статьи.
     *
     * @param SaveArticleRequestContract $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(SaveArticleRequestContract $request): RedirectResponse
    {
        return $this->save($request);
    }

    /**
     * Редактирование статьи.
     *
     * @param null $id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     *
     * @throws \Exception
     */
    public function edit($id = null): View
    {
        if (! is_null($id) && $id > 0 && $item = ArticleModel::find($id)) {
            $table = $this->generateTable('products', 'embedded');

            return view('admin.module.articles::back.pages.form', [
                'item' => $item,
                'productsTable' => $table,
            ]);
        } else {
            abort(404);
        }
    }

    /**
     * Обновление статьи.
     *
     * @param SaveArticleRequestContract $request
     * @param null $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(SaveArticleRequestContract $request, $id = null): RedirectResponse
    {
        return $this->save($request, $id);
    }

    /**
     * Сохранение статьи.
     *
     * @param SaveArticleRequestContract $request
     * @param null $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    private function save(SaveArticleRequestContract $request, $id = null): RedirectResponse
    {
        if (! is_null($id) && $id > 0 && $item = ArticleModel::find($id)) {
            $action = 'отредактирована';
        } else {
            $action = 'создана';
            $item = new ArticleModel();
        }

        $item->title = strip_tags($request->get('title'));
        $item->slug = strip_tags($request->get('slug'));
        $item->description = strip_tags($request->input('description.text'));
        $item->content = $request->input('content.text');
        $item->publish_date = ($request->filled('publish_date')) ? date('Y-m-d H:i', \DateTime::createFromFormat('!d.m.Y H:i', $request->get('publish_date'))->getTimestamp()) : null;
        $item->status_id = ($request->filled('status_id')) ? $request->get('status_id') : 1;
        $item->save();

        $this->saveAccess($item, $request);
        $this->saveMeta($item, $request);
        $this->saveCategories($item, $request);
        $this->saveIngredients($item, $request);
        $this->saveTags($item, $request);
        $this->saveClassifiers($item, $request);
        $this->saveProducts($item, $request);
        $this->saveImages($item, $request, ['og_image', 'preview', 'content'], 'articles');

        // Обновление поискового индекса.
        $item->searchable();

        event(new ModifyArticleEvent($item));

        Session::flash('success', 'Статья «'.$item->title.'» успешно '.$action);

        return response()->redirectToRoute('back.articles.edit', [
            $item->fresh()->id,
        ]);
    }

    /**
     * Удаление статьи.
     *
     * @param null $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id = null): JsonResponse
    {
        if (! is_null($id) && $id > 0 && $item = ArticleModel::find($id)) {

            $item->delete();

            event(new ModifyArticleEvent($item));

            return response()->json([
                'success' => true,
            ]);
        } else {
            return response()->json([
                'success' => false,
            ]);
        }
    }
}
