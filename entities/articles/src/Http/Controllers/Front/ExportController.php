<?php

namespace InetStudio\ArticlesPackage\Articles\Http\Controllers\Front;

use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use InetStudio\AdminPanel\Base\Http\Controllers\Controller;
use Illuminate\Contracts\Container\BindingResolutionException;
use InetStudio\ArticlesPackage\Articles\Contracts\Http\Controllers\Front\ExportControllerContract;

/**
 * Class ExportController.
 */
class ExportController extends Controller implements ExportControllerContract
{
    /**
     * Экспортируем комментарии.
     *
     * @param string $slug
     *
     * @return BinaryFileResponse
     *
     * @throws BindingResolutionException
     */
    public function exportComments(string $slug)
    {
        $export = app()->make('InetStudio\ArticlesPackage\Articles\Contracts\Exports\CommentsExportContract', compact('slug'));

        return Excel::download($export, time().'.xlsx');
    }

    /**
     * Экспортируем изображения.
     *
     * @param string $slug
     *
     * @return BinaryFileResponse
     *
     * @throws BindingResolutionException
     */
    public function exportImages(string $slug)
    {
        $export = app()->make('InetStudio\ArticlesPackage\Articles\Contracts\Exports\ImagesExportContract', compact('slug'));

        return Excel::download($export, time().'.xlsx');
    }
}
