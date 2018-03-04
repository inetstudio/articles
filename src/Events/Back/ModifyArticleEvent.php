<?php

namespace InetStudio\Articles\Events\Back;

use Illuminate\Queue\SerializesModels;
use InetStudio\Articles\Contracts\Events\Back\ModifyArticleEventContract;

class ModifyArticleEvent implements ModifyArticleEventContract
{
    use SerializesModels;

    public $object;

    /**
     * ModifyArticleEvent constructor.
     *
     * @param $object
     */
    public function __construct($object)
    {
        $this->object = $object;
    }
}
