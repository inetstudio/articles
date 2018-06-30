<?php

namespace InetStudio\Articles\Http\Responses\Back\Articles;

use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Support\Responsable;
use InetStudio\Articles\Contracts\Models\ArticleModelContract;
use InetStudio\Articles\Contracts\Http\Responses\Back\Articles\ShowResponseContract;

/**
 * Class ShowResponse.
 */
class ShowResponse implements ShowResponseContract, Responsable
{
    /**
     * @var ArticleModelContract
     */
    private $item;

    /**
     * ShowResponse constructor.
     *
     * @param ArticleModelContract $item
     */
    public function __construct(ArticleModelContract $item)
    {
        $this->item = $item;
    }

    /**
     * Возвращаем ответ при получении объекта.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return JsonResponse
     */
    public function toResponse($request): JsonResponse
    {
        return response()->json($this->item);
    }
}
