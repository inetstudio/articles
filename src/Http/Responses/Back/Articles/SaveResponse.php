<?php

namespace InetStudio\Articles\Http\Responses\Back\Articles;

use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\Support\Responsable;
use InetStudio\Articles\Contracts\Models\ArticleModelContract;
use InetStudio\Articles\Contracts\Http\Responses\Back\Articles\SaveResponseContract;

/**
 * Class SaveResponse.
 */
class SaveResponse implements SaveResponseContract, Responsable
{
    /**
     * @var ArticleModelContract
     */
    private $item;

    /**
     * SaveResponse constructor.
     *
     * @param ArticleModelContract $item
     */
    public function __construct(ArticleModelContract $item)
    {
        $this->item = $item;
    }

    /**
     * Возвращаем ответ при сохранении объекта.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return RedirectResponse
     */
    public function toResponse($request): RedirectResponse
    {
        return response()->redirectToRoute('back.articles.edit', [
            $this->item->fresh()->id,
        ]);
    }
}
