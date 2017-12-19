<?php

namespace InetStudio\Articles\Http\Controllers\Back;

use Illuminate\View\View;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;
use InetStudio\Articles\Models\ArticleModel;
use InetStudio\Categories\Models\CategoryModel;
use InetStudio\Articles\Events\ModifyArticleEvent;
use Cviebrock\EloquentSluggable\Services\SlugService;
use InetStudio\Articles\Transformers\ArticleTransformer;
use InetStudio\Articles\Http\Requests\Back\SaveArticleRequest;
use InetStudio\AdminPanel\Http\Controllers\Back\Traits\DatatablesTrait;
use InetStudio\Meta\Http\Controllers\Back\Traits\MetaManipulationsTrait;
use InetStudio\Tags\Http\Controllers\Back\Traits\TagsManipulationsTrait;
use InetStudio\AdminPanel\Http\Controllers\Back\Traits\ImagesManipulationsTrait;
use InetStudio\Products\Http\Controllers\Back\Traits\ProductsManipulationsTrait;
use InetStudio\Categories\Http\Controllers\Back\Traits\CategoriesManipulationsTrait;
use InetStudio\Classifiers\Http\Controllers\Back\Traits\ClassifiersManipulationsTrait;
use InetStudio\Ingredients\Http\Controllers\Back\Traits\IngredientsManipulationsTrait;

/**
 * Контроллер для управления статьями.
 *
 * Class ContestByTagStatusesController
 */
class ArticlesController extends Controller
{
    use DatatablesTrait;
    use MetaManipulationsTrait;
    use TagsManipulationsTrait;
    use ImagesManipulationsTrait;
    use ProductsManipulationsTrait;
    use CategoriesManipulationsTrait;
    use ClassifiersManipulationsTrait;
    use IngredientsManipulationsTrait;

    /**
     * Список статей.
     *
     * @param DataTables $dataTable
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Exception
     */
    public function index(DataTables $dataTable): View
    {
        $table = $this->generateTable($dataTable, 'articles', 'index');

        return view('admin.module.articles::back.pages.index', compact('table'));
    }

    /**
     * DataTables ServerSide.
     *
     * @return mixed
     * @throws \Exception
     */
    public function data()
    {
        $items = ArticleModel::with('status');

        return DataTables::of($items)
            ->setTransformer(new ArticleTransformer)
            ->rawColumns(['status', 'actions'])
            ->make();
    }

    /**
     * Добавление статьи.
     *
     * @param DataTables $dataTable
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Exception
     */
    public function create(DataTables $dataTable): View
    {
        $table = $this->generateTable($dataTable, 'products', 'embedded');

        $categories = CategoryModel::getTree();

        return view('admin.module.articles::back.pages.form', [
            'item' => new ArticleModel(),
            'categories' => $categories,
            'productsTable' => $table,
        ]);
    }

    /**
     * Создание статьи.
     *
     * @param SaveArticleRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(SaveArticleRequest $request): RedirectResponse
    {
        return $this->save($request);
    }

    /**
     * Редактирование статьи.
     *
     * @param DataTables $dataTable
     * @param null $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Exception
     */
    public function edit(DataTables $dataTable, $id = null): View
    {
        if (! is_null($id) && $id > 0 && $item = ArticleModel::find($id)) {
            $categories = CategoryModel::getTree();

            $table = $this->generateTable($dataTable, 'products', 'embedded');

            return view('admin.module.articles::back.pages.form', [
                'item' => $item,
                'categories' => $categories,
                'productsTable' => $table,
            ]);
        } else {
            abort(404);
        }
    }

    /**
     * Обновление статьи.
     *
     * @param SaveArticleRequest $request
     * @param null $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(SaveArticleRequest $request, $id = null): RedirectResponse
    {
        return $this->save($request, $id);
    }

    /**
     * Сохранение статьи.
     *
     * @param SaveArticleRequest $request
     * @param null $id
     * @return \Illuminate\Http\RedirectResponse
     */
    private function save(SaveArticleRequest $request, $id = null): RedirectResponse
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

    /**
     * Получаем slug для модели по строке.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSlug(Request $request): JsonResponse
    {
        $name = $request->get('name');
        $slug = SlugService::createSlug(ArticleModel::class, 'slug', $name);

        return response()->json($slug);
    }

    /**
     * Возвращаем статьи для поля.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSuggestions(Request $request): JsonResponse
    {
        $data = [];

        if ($request->filled('type') and $request->get('type') == 'autocomplete') {
            $search = $request->get('query');
            $data['suggestions'] = [];

            $articles = ArticleModel::where('title', 'LIKE', '%'.$search.'%')->get();

            foreach ($articles as $article) {
                $data['suggestions'][] = [
                    'value' => $article->title,
                    'data' => [
                        'id' => $article->id,
                        'title' => $article->title,
                        'href' => url($article->href),
                        'preview' => ($article->getFirstMedia('preview')) ? url($article->getFirstMedia('preview')->getUrl('preview_sidebar')) : '',
                    ],
                ];
            }
        } else {
            $search = $request->get('q');

            $data['items'] = ArticleModel::select(['id', 'title as name'])->where('title', 'LIKE', '%'.$search.'%')->get()->toArray();
        }

        return response()->json($data);
    }
}
