<?php

namespace InetStudio\Articles\Http\Responses\Back\Articles;

use Illuminate\View\View;
use Illuminate\Contracts\Support\Responsable;
use InetStudio\Articles\Contracts\Http\Responses\Back\Articles\IndexResponseContract;

/**
 * Class IndexResponse.
 */
class IndexResponse implements IndexResponseContract, Responsable
{
    /**
     * @var array
     */
    private $data;

    /**
     * IndexResponse constructor.
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Возвращаем ответ при открытии списка объектов.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return View
     */
    public function toResponse($request): View
    {
        return view('admin.module.articles::back.pages.index', $this->data);
    }
}
