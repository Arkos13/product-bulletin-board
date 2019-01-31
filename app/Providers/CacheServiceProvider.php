<?php

namespace App\Providers;

use App\Entity\Region;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class CacheServiceProvider extends ServiceProvider
{
    private $classes = [
        Region::class
    ];

    public function boot()
    {
        foreach ($this->classes as $class) {
            $this->registerFlusher($class);
        }
    }

    private function registerFlusher($class)
    {
        $flush = function() use ($class) {
            Cache::tags($class)->flush();
        };
        /** @var Model $class */
        $class::created($flush);
        $class::saved($flush);
        $class::updated($flush);
        $class::deleted($flush);
    }

}