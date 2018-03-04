<?php

namespace InetStudio\Articles\Http\Responses\Back\Articles;

use Illuminate\View\View;
use Illuminate\Contracts\Support\Responsable;
use InetStudio\Articles\Contracts\Http\Responses\Back\Articles\FormResponseContract;

/**
 * Class FormResponse.
 */
class FormResponse implements FormResponseContract, Responsable
{
    /**
     * @var array
     */
    private $data;

    /**
     * FormResponse constructor.
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Возвращаем ответ при открытии формы объекта.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return View
     */
    public function toResponse($request): View
    {
        return view('admin.module.articles::back.pages.form', $this->data);
    }
}
