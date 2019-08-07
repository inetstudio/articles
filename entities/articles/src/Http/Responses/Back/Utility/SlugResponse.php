<?php

namespace InetStudio\ArticlesPackage\Articles\Http\Responses\Back\Utility;

use Illuminate\Http\Request;
use InetStudio\ArticlesPackage\Articles\Contracts\Http\Responses\Back\Utility\SlugResponseContract;

/**
 * Class SlugResponse.
 */
class SlugResponse implements SlugResponseContract
{
    /**
     * @var string
     */
    protected $slug;

    /**
     * SlugResponse constructor.
     *
     * @param  string  $slug
     */
    public function __construct(string $slug)
    {
        $this->slug = $slug;
    }

    /**
     * Возвращаем slug по заголовку объекта.
     *
     * @param  Request  $request
     *
     * @return \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function toResponse($request)
    {
        return response()->json($this->slug);
    }
}
