<?php

namespace InetStudio\Articles\Http\Controllers\Back;

use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use InetStudio\Articles\Models\ArticleModel;
use InetStudio\Articles\Transformers\Back\ArticleTransformer;
use InetStudio\Articles\Contracts\Http\Controllers\Back\ArticlesDataControllerContract;

/**
 * Class ArticlesDataController.
 */
class ArticlesDataController extends Controller implements ArticlesDataControllerContract
{
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
}
