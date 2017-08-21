<?php

namespace InetStudio\Articles\Controllers;

use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\Controller;
use InetStudio\Tags\Models\TagModel;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use InetStudio\Articles\Models\ArticleModel;
use InetStudio\Categories\Models\CategoryModel;
use InetStudio\Ingredients\Models\IngredientModel;
use InetStudio\Articles\Requests\SaveArticleRequest;
use Cviebrock\EloquentSluggable\Services\SlugService;
use InetStudio\Articles\Transformers\ArticleTransformer;

/**
 * Контроллер для управления статьями.
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

        $table->columns($this->getColumns());
        $table->ajax($this->getAjaxOptions());
        $table->parameters($this->getTableParameters());

        return view('admin.module.articles::pages.articles.index', compact('table'));
    }

    /**
     * Свойства колонок datatables.
     *
     * @return array
     */
    private function getColumns()
    {
        return [
            ['data' => 'title', 'name' => 'title', 'title' => 'Заголовок'],
            ['data' => 'created_at', 'name' => 'created_at', 'title' => 'Дата создания'],
            ['data' => 'updated_at', 'name' => 'updated_at', 'title' => 'Дата обновления'],
            ['data' => 'actions', 'name' => 'actions', 'title' => 'Действия', 'orderable' => false, 'searchable' => false],
        ];
    }

    /**
     * Свойства ajax datatables.
     *
     * @return array
     */
    private function getAjaxOptions()
    {
        return [
            'url' => route('back.articles.data'),
            'type' => 'POST',
            'data' => 'function(data) { data._token = $(\'meta[name="csrf-token"]\').attr(\'content\'); }',
        ];
    }

    /**
     * Свойства datatables.
     *
     * @return array
     */
    private function getTableParameters()
    {
        return [
            'paging' => true,
            'pagingType' => 'full_numbers',
            'searching' => true,
            'info' => false,
            'searchDelay' => 350,
            'language' => [
                'url' => asset('admin/js/plugins/datatables/locales/russian.json'),
            ],
        ];
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
        if (! is_null($id) && $id > 0 && $item = ArticleModel::find($id)) {
            $categories = CategoryModel::getTree();

            return view('admin.module.articles::pages.articles.form', [
                'item' => $item,
                'categories' => $categories,
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
        $item->publish_date = ($request->has('publish_date')) ? date('Y-m-d H:i', \DateTime::createFromFormat('!d.m.Y H:i', $request->get('publish_date'))->getTimestamp()) : null;
        $item->save();

        $this->saveMeta($item, $request);
        $this->saveCategories($item, $request);
        $this->saveIngredients($item, $request);
        $this->saveTags($item, $request);
        $this->saveImages($item, $request, ['og_image', 'preview', 'content']);

        Session::flash('success', 'Статья «'.$item->title.'» успешно '.$action);

        return redirect()->to(route('back.articles.edit', $item->fresh()->id));
    }

    /**
     * Сохраняем мета теги.
     *
     * @param ArticleModel $item
     * @param SaveArticleRequest $request
     */
    private function saveMeta($item, $request)
    {
        if ($request->has('meta')) {
            foreach ($request->get('meta') as $key => $value) {
                $item->updateMeta($key, $value);
            }
        }
    }

    /**
     * Сохраняем категории.
     *
     * @param ArticleModel $item
     * @param SaveArticleRequest $request
     */
    private function saveCategories($item, $request)
    {
        if ($request->has('categories')) {
            $categories = explode(',', $request->get('categories'));
            $item->recategorize(CategoryModel::whereIn('id', $categories)->get());
        } else {
            $item->uncategorize($item->categories);
        }
    }

    /**
     * Сохраняем ингредиенты.
     *
     * @param ArticleModel $item
     * @param SaveArticleRequest $request
     */
    private function saveIngredients($item, $request)
    {
        if ($request->has('ingredients')) {
            $item->syncIngredients(IngredientModel::whereIn('id', (array) $request->get('ingredients'))->get());
        } else {
            $item->detachIngredients($item->categories);
        }
    }

    /**
     * Сохраняем теги.
     *
     * @param ArticleModel $item
     * @param SaveArticleRequest $request
     */
    private function saveTags($item, $request)
    {
        if ($request->has('tags')) {
            $item->syncTags(TagModel::whereIn('id', (array) $request->get('tags'))->get());
        } else {
            $item->detachTags($item->tags);
        }
    }

    /**
     * Сохраняем изображения.
     *
     * @param ArticleModel $item
     * @param SaveArticleRequest $request
     * @param array $images
     */
    private function saveImages($item, $request, $images)
    {
        foreach ($images as $name) {
            $properties = $request->get($name);

            if (isset($properties['images'])) {
                $item->clearMediaCollectionExcept($name, $properties['images']);

                foreach ($properties['images'] as $image) {
                    if ($image['id']) {
                        $media = $item->media->find($image['id']);
                        $media->custom_properties = $image['properties'];
                        $media->save();
                    } else {
                        $filename = $image['filename'];

                        $file = Storage::disk('temp')->getDriver()->getAdapter()->getPathPrefix().$image['tempname'];

                        $item->addMedia($file)
                            ->withCustomProperties($image['properties'])
                            ->usingName(pathinfo($filename, PATHINFO_FILENAME))
                            ->usingFileName($image['tempname'])
                            ->toMediaCollection($name, 'articles');
                    }
                }
            } else {
                if (isset($properties['tempname']) && isset($properties['filename'])) {
                    $image = $properties['tempname'];
                    $filename = $properties['filename'];

                    $item->clearMediaCollection($name);

                    array_forget($properties, ['tempname', 'temppath', 'filename']);
                    $properties = array_filter($properties);

                    $file = Storage::disk('temp')->getDriver()->getAdapter()->getPathPrefix().$image;

                    $item->addMedia($file)
                        ->withCustomProperties($properties)
                        ->usingName(pathinfo($filename, PATHINFO_FILENAME))
                        ->usingFileName($image)
                        ->toMediaCollection($name, 'articles');
                } else {
                    $properties = array_filter($properties);

                    $media = $item->getFirstMedia($name);

                    if ($media) {
                        $media->custom_properties = $properties;
                        $media->save();
                    }
                }
            }
        }
    }

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
}
