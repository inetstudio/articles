<?php

namespace InetStudio\ArticlesPackage\Articles\Events\Back;

use Illuminate\Queue\SerializesModels;
use InetStudio\ArticlesPackage\Articles\Contracts\Models\ArticleModelContract;
use InetStudio\ArticlesPackage\Articles\Contracts\Events\Back\ModifyItemEventContract;

/**
 * Class ModifyItemEvent.
 */
class ModifyItemEvent implements ModifyItemEventContract
{
    use SerializesModels;

    /**
     * @var ArticleModelContract
     */
    public $item;

    /**
     * ModifyItemEvent constructor.
     *
     * @param  ArticleModelContract  $item
     */
    public function __construct(ArticleModelContract $item)
    {
        $this->item = $item;
    }
}
