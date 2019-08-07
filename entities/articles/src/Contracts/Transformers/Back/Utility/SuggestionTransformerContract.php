<?php

namespace InetStudio\ArticlesPackage\Articles\Contracts\Transformers\Back\Utility;

/**
 * Interface SuggestionTransformerContract.
 */
interface SuggestionTransformerContract
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
