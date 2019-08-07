<?php

namespace InetStudio\ArticlesPackage\Articles\Transformers\Back\Utility;

use Throwable;
use InetStudio\AdminPanel\Base\Transformers\BaseTransformer;
use InetStudio\ArticlesPackage\Articles\Contracts\Transformers\Back\Utility\SuggestionTransformerContract;

/**
 * Class SuggestionTransformer.
 */
class SuggestionTransformer extends BaseTransformer implements SuggestionTransformerContract
{
    /**
     * @var string
     */
    protected $type;

    /**
     * SuggestionTransformer constructor.
     *
     * @param  string  $type
     */
    public function __construct(string $type = '')
    {
        $this->type = $type;
    }

    /**
     * Подготовка данных для отображения в выпадающих списках.
     *
     * @param $item
     *
     * @return array
     *
     * @throws Throwable
     */
    public function transform($item): array
    {
        return ($this->type == 'autocomplete')
            ? [
                'value' => $item['title'],
                'data' => [
                    'id' => $item['id'],
                    'type' => get_class($item),
                    'title' => $item['title'],
                    'path' => parse_url($item['href'], PHP_URL_PATH),
                    'href' => $item['href'],
                ],
            ]
            : [
                'id' => $item['id'],
                'name' => $item['title'],
            ];
    }
}
