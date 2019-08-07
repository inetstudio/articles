<?php

namespace InetStudio\ArticlesPackage\Articles\Contracts\Transformers\Back\Resource;

/**
 * Interface IndexTransformerContract.
 */
interface IndexTransformerContract
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
