<?php

namespace App\Http\Router;

use App\Entity\Adverts\Category;
use App\Entity\Region;
use Illuminate\Contracts\Routing\UrlRoutable;
use Illuminate\Support\Facades\Cache;

class AdvertsPath implements UrlRoutable
{
    /**
     * @var Region
     */
    public $region;
    /**
     * @var Category
     */
    public $category;

    /**
     * @param Region|null $region
     * @return AdvertsPath
     */
    public function withRegion(?Region $region): self
    {
        $clone = clone $this;
        $clone->region = $region;
        return $clone;
    }

    /**
     * @param Category|null $category
     * @return AdvertsPath
     */
    public function withCategory(?Category $category): self
    {
        $clone = clone $this;
        $clone->category = $category;
        return $clone;
    }

    /**
     * Get the value of the model's route key.
     *
     * @return mixed
     */
    public function getRouteKey()
    {
        $segments = [];

        if ($this->region) {
            $segments[] = Cache::tags(Region::class)->rememberForever('region_path_' . $this->region->id, function () {
                return $this->region->getPath();
            });
        }

        if ($this->category) {
            $segments[] = Cache::tags(Category::class)->rememberForever('category_path_' . $this->category->id, function () {
                return $this->category->getPath();
            });
        }

        return implode('/', $segments);
    }

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'adverts_path';
    }

    /**
     * Retrieve the model for a bound value.
     *
     * @param  mixed $value
     * @return AdvertsPath
     */
    public function resolveRouteBinding($value)
    {
        $chunks = explode('/', $value);

        /** @var Region|null $region */
        $region = null;

        do {
            $slug = reset($chunks);
            if ($slug && $next = Region::query()->where('slug', $slug)->where('parent_id', $region ? $region->id : null)->first()) {
                $region = $next;
                array_shift($chunks);
            }
        } while (!empty($slug) && !empty($next));

        /** @var Category|null $category */
        $category = null;
        do {
            $slug = reset($chunks);
            if ($slug && $next = Category::query()->where('slug', $slug)->where('parent_id', $category ? $category->id : null)->first()) {
                $category = $next;
                array_shift($chunks);
            }
        } while (!empty($slug) && !empty($next));

        if (!empty($chunks)) {
            abort(404);
        }

        return $this
            ->withRegion($region)
            ->withCategory($category);
    }
}