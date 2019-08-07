<?php

namespace InetStudio\ArticlesPackage\Articles\Http\Responses\Back\Resource;

use Illuminate\Http\Request;
use InetStudio\ArticlesPackage\Articles\Contracts\Models\ArticleModelContract;
use InetStudio\ArticlesPackage\Articles\Contracts\Http\Responses\Back\Resource\SaveResponseContract;

/**
 * Class SaveResponse.
 */
class SaveResponse implements SaveResponseContract
{
    /**
     * @var ArticleModelContract
     */
    protected $item;

    /**
     * SaveResponse constructor.
     *
     * @param  ArticleModelContract  $item
     */
    public function __construct(ArticleModelContract $item)
    {
        $this->item = $item;
    }

    /**
     * Возвращаем ответ при сохранении объекта.
     *
     * @param  Request  $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function toResponse($request)
    {
        $item = $this->item->fresh();

        return response()->redirectToRoute(
            'back.articles.edit',
            [
                $item['id'],
            ]
        );
    }
}
