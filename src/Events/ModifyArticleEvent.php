<?php

namespace InetStudio\Articles\Events;

use Illuminate\Queue\SerializesModels;
use InetStudio\Articles\Contracts\Events\ModifyArticleEventContract;

class ModifyArticleEvent implements ModifyArticleEventContract
{
    use SerializesModels;

    public $object;

    /**
     * Create a new event instance.
     *
     * ModifyArticleEvent constructor.
     * @param $object
     */
    public function __construct($object)
    {
        $this->object = $object;
    }
}
