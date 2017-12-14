<?php

namespace InetStudio\Articles\Listeners;

use Illuminate\Support\Facades\Cache;

class ClearArticlesCacheListener
{
    /**
     * ClearArticlesCacheListener constructor.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param $event
     */
    public function handle($event): void
    {
        Cache::tags(['materials'])->flush();
        Cache::tags(['articles'])->flush();
    }
}
