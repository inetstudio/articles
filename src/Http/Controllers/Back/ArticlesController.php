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
use InetStudio\Articles\Events\ModifyArticleEvent;
use Cviebrock\EloquentSluggable\Services\SlugService;
use InetStudio\Articles\Transformers\Back\ArticleTransformer;
use InetStudio\Articles\Http\Requests\Back\SaveArticleRequest;
use InetStudio\AdminPanel\Http\Controllers\Back\Traits\DatatablesTrait;
use InetStudio\Meta\Http\Controllers\Back\Traits\MetaManipulationsTrait;
use InetStudio\Tags\Http\Controllers\Back\Traits\TagsManipulationsTrait;
use InetStudio\Access\Http\Controllers\Back\Traits\AccessManipulationsTrait;
use InetStudio\AdminPanel\Http\Controllers\Back\Traits\ImagesManipulationsTrait;
use InetStudio\Products\Http\Controllers\Back\Traits\ProductsManipulationsTrait;
use InetStudio\Categories\Http\Controllers\Back\Traits\CategoriesManipulationsTrait;
use InetStudio\Classifiers\Http\Controllers\Back\Traits\ClassifiersManipulationsTrait;
use InetStudio\Ingredients\Http\Controllers\Back\Traits\IngredientsManipulationsTrait;

/**
 * Контроллер для управления статьями.
 *
 * Class ArticlesController
 * @package InetStudio\Articles\Http\Controllers\Back
 */
class ArticlesController extends Controller
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
     * DataTables ServerSide.
     *
     * @return mixed
     *
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
     * @param SaveArticleRequest $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(SaveArticleRequest $request): RedirectResponse
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
     * @param SaveArticleRequest $request
     * @param null $id
     *
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
     *
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

    /**
     * Получаем slug для модели по строке.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSlug(Request $request): JsonResponse
    {
        $name = $request->get('name');
        $slug = ($name) ? SlugService::createSlug(ArticleModel::class, 'slug', $name) : '';

        return response()->json($slug);
    }

    /**
     * Возвращаем статьи для поля.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSuggestions(Request $request): JsonResponse
    {
        $search = $request->get('q');

        $items = ArticleModel::select(['id', 'title', 'slug'])->where('title', 'LIKE', '%'.$search.'%')->get();

        if ($request->filled('type') && $request->get('type') == 'autocomplete') {
            $type = get_class(new ArticleModel());

            $data = $items->mapToGroups(function ($item) use ($type) {
                return [
                    'suggestions' => [
                        'value' => $item->title,
                        'data' => [
                            'id' => $item->id,
                            'type' => $type,
                            'title' => $item->title,
                            'path' => parse_url($item->href, PHP_URL_PATH),
                            'href' => $item->href,
                            'preview' => ($item->getFirstMedia('preview')) ? url($item->getFirstMedia('preview')->getUrl('preview_sidebar')) : '',
                        ],
                    ],
                ];
            });
        } else {
            $data = $items->mapToGroups(function ($item) {
                return [
                    'items' => [
                        'id' => $item->id,
                        'name' => $item->title,
                    ],
                ];
            });
        }

        return response()->json($data);
    }
}
