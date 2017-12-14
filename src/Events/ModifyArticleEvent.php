<?php

namespace InetStudio\Articles\Events;

use Illuminate\Queue\SerializesModels;

class ModifyArticleEvent
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
