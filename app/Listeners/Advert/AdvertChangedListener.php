<?php

namespace App\Listeners\Advert;

use App\Jobs\Advert\ReindexAdvert;

class AdvertChangedListener
{
    /**
     * @param $event
     */
    public function handle($event)
    {
        ReindexAdvert::dispatch($event->advert);
    }
}