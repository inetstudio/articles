<?php

namespace InetStudio\ArticlesPackage\Articles\Http\Responses\Back\Resource;

use Illuminate\Http\Request;
use InetStudio\ArticlesPackage\Articles\Contracts\Models\ArticleModelContract;
use InetStudio\ArticlesPackage\Articles\Contracts\Http\Responses\Back\Resource\ShowResponseContract;

/**
 * Class ShowResponse.
 */
class ShowResponse implements ShowResponseContract
{
    /**
     * @var ArticleModelContract
     */
    protected $item;

    /**
     * ShowResponse constructor.
     *
     * @param  ArticleModelContract  $item
     */
    public function __construct(ArticleModelContract $item)
    {
        $this->item = $item;
    }

    /**
     * Возвращаем ответ при получении объекта.
     *
     * @param  Request  $request
     *
     * @return \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function toResponse($request)
    {
        return response()->json($this->item);
    }
}
