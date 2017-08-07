<?php

namespace InetStudio\Articles\Controllers;

use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\Controller;
use InetStudio\Tags\Models\TagModel;
use Illuminate\Support\Facades\Session;
use InetStudio\Articles\Models\ArticleModel;
use InetStudio\Categories\Models\CategoryModel;
use InetStudio\Articles\Requests\SaveArticleRequest;
use Cviebrock\EloquentSluggable\Services\SlugService;
use InetStudio\Articles\Transformers\ArticleTransformer;

/**
 * Контроллер для управления статьими.
 *
 * Class ContestByTagStatusesController
 */
class ArticlesController extends Controller
{
    /**
     * Список статей.
     *
     * @param Datatables $dataTable
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Datatables $dataTable)
    {
        $table = $dataTable->getHtmlBuilder();

        $table->columns([
            ['data' => 'title', 'name' => 'title', 'title' => 'Заголовок'],
            ['data' => 'created_at', 'name' => 'created_at', 'title' => 'Дата создания'],
            ['data' => 'updated_at', 'name' => 'updated_at', 'title' => 'Дата обновления'],
            ['data' => 'actions', 'name' => 'actions', 'title' => 'Действия', 'orderable' => false, 'searchable' => false],
        ]);

        $table->ajax([
            'url' => route('back.articles.data'),
            'type' => 'POST',
            'data' => 'function(data) { data._token = $(\'meta[name="csrf-token"]\').attr(\'content\'); }',
        ]);

        $table->parameters([
            'paging' => true,
            'pagingType' => 'full_numbers',
            'searching' => true,
            'info' => false,
            'searchDelay' => 350,
            'language' => [
                'url' => asset('admin/js/plugins/datatables/locales/russian.json'),
            ],
        ]);

        return view('admin.module.articles::pages.articles.index', compact('table'));
    }

    /**
     * Datatables serverside.
     *
     * @return mixed
     */
    public function data()
    {
        $items = ArticleModel::query();

        return Datatables::of($items)
            ->setTransformer(new ArticleTransformer)
            ->escapeColumns(['actions'])
            ->make();
    }

    /**
     * Добавление статьи.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $categories = CategoryModel::getTree();

        return view('admin.module.articles::pages.articles.form', [
            'item' => new ArticleModel(),
            'categories' => $categories,
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
     * @param null $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id = null)
    {
        if (! is_null($id) && $id > 0) {
            $item = ArticleModel::where('id', '=', $id)->first();
        } else {
            abort(404);
        }

        if (empty($item)) {
            abort(404);
        } else {
            $categories = CategoryModel::getTree();

            return view('admin.module.articles::pages.articles.form', [
                'item' => $item,
                'categories' => $categories,
            ]);
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
     * @param $request
     * @param null $id
     * @return \Illuminate\Http\RedirectResponse
     */
    private function save($request, $id = null)
    {
        if (! is_null($id) && $id > 0) {
            $edit = true;
            $item = ArticleModel::where('id', '=', $id)->first();

            if (empty($item)) {
                abort(404);
            }
        } else {
            $edit = false;
            $item = new ArticleModel();
        }

        $item->title = strip_tags($request->get('title'));
        $item->slug = strip_tags($request->get('slug'));
        $item->description = strip_tags($request->get('description'));
        $item->content = $request->get('content');
        $item->publish_date = ($request->has('publish_date')) ? date('Y-m-d H:i', \DateTime::createFromFormat('!d.m.Y H:i', $request->get('publish_date'))->getTimestamp()) : null;
        $item->save();

        if ($request->has('meta')) {
            foreach ($request->get('meta') as $key => $value) {
                $item->updateMeta($key, $value);
            }
        }

        if ($request->has('categories')) {
            $categories = explode(',', $request->get('categories'));
            $item->recategorize(CategoryModel::whereIn('id', $categories)->get());
        } else {
            $item->uncategorize($item->categories);
        }

        if ($request->has('tags')) {
            $item->syncTags(TagModel::whereIn('id', $request->get('tags'))->get());
        } else {
            $item->detachTags($item->tags);
        }

        foreach (['og_image', 'preview'] as $name) {
            $properties = $request->get($name);

            if (isset($properties['base64'])) {
                $image = $properties['base64'];
                $filename = $properties['filename'];

                array_forget($properties, 'base64');
                array_forget($properties, 'filename');
            }

            if (isset($image) && isset($filename)) {
                if (isset($properties['type']) && $properties['type'] == 'single') {
                    $item->clearMediaCollection($name);
                    array_forget($properties, 'type');
                }

                $properties = array_filter($properties);

                $item->addMediaFromBase64($image)
                    ->withCustomProperties($properties)
                    ->usingName(pathinfo($filename, PATHINFO_FILENAME))
                    ->usingFileName(md5($image).'.'.pathinfo($filename, PATHINFO_EXTENSION))
                    ->toMediaCollection($name, 'articles');
            } else {
                if (isset($properties['type']) && $properties['type'] == 'single') {
                    array_forget($properties, 'type');

                    $properties = array_filter($properties);

                    $media = $item->getFirstMedia($name);
                    $media->custom_properties = $properties;
                    $media->save();
                }
            }
        }

        $action = ($edit) ? 'отредактирована' : 'создана';
        Session::flash('success', 'Статья «'.$item->title.'» успешно '.$action);

        return redirect()->to(route('back.articles.edit', $item->fresh()->id));
    }

    /**
     * Удаление статьи.
     *
     * @param null $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id = null)
    {
        if (! is_null($id) && $id > 0) {
            $item = ArticleModel::where('id', '=', $id)->first();
        } else {
            return response()->json([
                'success' => false,
            ]);
        }

        if (empty($item)) {
            return response()->json([
                'success' => false,
            ]);
        }

        $item->delete();

        return response()->json([
            'success' => true,
        ]);
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
}
