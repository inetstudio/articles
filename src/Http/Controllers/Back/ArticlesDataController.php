<?php

namespace InetStudio\Articles\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use InetStudio\Articles\Contracts\Http\Controllers\Back\ArticlesDataControllerContract;

/**
 * Class ArticlesDataController.
 */
class ArticlesDataController extends Controller implements ArticlesDataControllerContract
{
    /**
     * Используемые сервисы.
     *
     * @var array
     */
    private $services;

    /**
     * ArticlesController constructor.
     */
    public function __construct()
    {
        $this->services['dataTables'] = app()->make('InetStudio\Articles\Contracts\Services\Back\ArticlesDataTableServiceContract');
    }

    /**
     * Получаем данные для отображения в таблице.
     *
     * @return mixed
     */
    public function data()
    {
        return $this->services['dataTables']->ajax();
    }
}
