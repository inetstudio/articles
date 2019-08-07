<?php

namespace InetStudio\ArticlesPackage\Articles\Contracts\Http\Controllers\Back;

use Illuminate\Http\JsonResponse;
use InetStudio\ArticlesPackage\Articles\Contracts\Services\Back\DataTableServiceContract;

/**
 * Interface DataControllerContract.
 */
interface DataControllerContract
{
    /**
     * Получаем данные для отображения в таблице.
     *
     * @param  DataTableServiceContract  $dataTableService
     *
     * @return JsonResponse
     */
    public function data(DataTableServiceContract $dataTableService): JsonResponse;
}
