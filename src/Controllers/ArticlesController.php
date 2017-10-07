<?php

namespace InetStudio\Articles\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use InetStudio\Articles\Models\ArticleModel;
use InetStudio\Categories\Models\CategoryModel;
//use InetStudio\Classifiers\Models\ClassifierModel;
use InetStudio\AdminPanel\Traits\DatatablesTrait;
use InetStudio\Tags\Traits\TagsManipulationsTrait;
use InetStudio\Articles\Requests\SaveArticleRequest;
use Cviebrock\EloquentSluggable\Services\SlugService;
use InetStudio\AdminPanel\Traits\MetaManipulationsTrait;
use InetStudio\Articles\Transformers\ArticleTransformer;
use InetStudio\AdminPanel\Traits\ImagesManipulationsTrait;
use InetStudio\Products\Traits\ProductsManipulationsTrait;
use InetStudio\Categories\Traits\CategoriesManipulationsTrait;
use InetStudio\Ingredients\Traits\IngredientsManipulationsTrait;

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
    use IngredientsManipulationsTrait;

    /**
     * Список статей.
     *
     * @param DataTables $dataTable
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(DataTables $dataTable)
    {
        $table = $this->generateTable($dataTable, 'articles', 'index');

        return view('admin.module.articles::pages.index', compact('table'));
    }

    /**
     * Datatables serverside.
     *
     * @return mixed
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
     */
    public function create(DataTables $dataTable)
    {
        $table = $this->generateTable($dataTable, 'products', 'embedded');

        $categories = CategoryModel::getTree();

        return view('admin.module.articles::pages.form', [
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
    public function store(SaveArticleRequest $request)
    {
        return $this->save($request);
    }

    /**
     * Редактирование статьи.
     *
     * @param DataTables $dataTable
     * @param null $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(DataTables $dataTable, $id = null)
    {
        if (! is_null($id) && $id > 0 && $item = ArticleModel::find($id)) {
            $categories = CategoryModel::getTree();

            $table = $this->generateTable($dataTable, 'products', 'embedded');

            return view('admin.module.articles::pages.form', [
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
    public function update(SaveArticleRequest $request, $id = null)
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
    private function save($request, $id = null)
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
        //$this->saveClassifiers($item, $request);
        $this->saveIngredients($item, $request);
        $this->saveTags($item, $request);
        $this->saveProducts($item, $request);
        $this->saveImages($item, $request, ['og_image', 'preview', 'content'], 'articles');

        \Event::fire('inetstudio.articles.cache.clear');

        Session::flash('success', 'Статья «'.$item->title.'» успешно '.$action);

        return redirect()->to(route('back.articles.edit', $item->fresh()->id));
    }

    /**
     * Сохраняем классификаторы.
     *
     * @param ArticleModel $item
     * @param SaveArticleRequest $request
     */
    /*
    private function saveClassifiers($item, $request)
    {
        if ($request->filled('classifiers')) {
            $item->syncIngredients(IngredientModel::whereIn('id', (array) $request->get('classifiers'))->get());
        } else {
            $item->detachIngredients($item->categories);
        }
    }
    */

    /**
     * Удаление статьи.
     *
     * @param null $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id = null)
    {
        if (! is_null($id) && $id > 0 && $item = ArticleModel::find($id)) {
            $item->delete();

            \Event::fire('inetstudio.articles.cache.clear');

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
    public function getSlug(Request $request)
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
    public function getSuggestions(Request $request)
    {
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
                    ]
                ];
            }
        } else {
            $search = $request->get('q');
            $data = [];

            $data['items'] = ArticleModel::select(['id', 'title as name'])->where('title', 'LIKE', '%'.$search.'%')->get()->toArray();
        }

        return response()->json($data);
    }
}
