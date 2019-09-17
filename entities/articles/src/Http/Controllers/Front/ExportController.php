<?php

namespace InetStudio\ArticlesPackage\Articles\Http\Controllers\Front;

use Illuminate\Http\Request;
use InetStudio\AdminPanel\Base\Http\Controllers\Controller;
use InetStudio\ArticlesPackage\Articles\Contracts\Http\Controllers\Front\ExportControllerContract;
use InetStudio\ArticlesPackage\Articles\Contracts\Http\Responses\Front\Export\ImagesExportResponseContract;
use InetStudio\ArticlesPackage\Articles\Contracts\Http\Responses\Front\Export\CommentsExportResponseContract;

/**
 * Class ExportController.
 */
class ExportController extends Controller implements ExportControllerContract
{
    /**
     * Экспортируем комментарии.
     *
     * @param  Request  $request
     * @param  CommentsExportResponseContract  $response
     *
     * @return CommentsExportResponseContract
     */
    public function exportComments(Request $request, CommentsExportResponseContract $response): CommentsExportResponseContract
    {
        return $response;
    }

    /**
     * Экспортируем изображения.
     *
     * @param  Request  $request
     * @param  ImagesExportResponseContract  $response
     *
     * @return ImagesExportResponseContract
     */
    public function exportImages(Request $request, ImagesExportResponseContract $response): ImagesExportResponseContract
    {
        return $response;
    }
}
