<?php

namespace InetStudio\ArticlesPackage\Articles\Contracts\Transformers\Front\Sitemap;

/**
 * Interface ItemTransformerContract.
 */
interface ItemTransformerContract
{
    /**
     * Трансформация данных.
     *
     * @param $item
     *
     * @return array
     */
    public function transform($item): array;
}
