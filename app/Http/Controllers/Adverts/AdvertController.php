<?php

namespace App\Http\Controllers\Adverts;

use App\Entity\Adverts\Advert\Advert;
use App\Entity\Adverts\Category;
use App\Entity\Region;
use App\Http\Controllers\Controller;
use App\Http\Router\AdvertsPath;
use Illuminate\Support\Facades\Gate;

class AdvertController extends Controller
{
    /**
     * @param AdvertsPath $path
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(AdvertsPath $path)
    {
        $query = Advert::active()->with(['category', 'region'])->orderByDesc('published_at');
        if ($category = $path->category) {
            $query->forCategory($category);
        }
        if ($region = $path->region) {
            $query->forRegion($region);
        }
        $regions = $region
            ? $region->children()->orderBy('name')->getModels()
            : Region::roots()->orderBy('name')->getModels();
        $categories = $category
            ? $category->children()->defaultOrder()->getModels()
            : Category::whereIsRoot()->defaultOrder()->getModels();
        $adverts = $query->paginate(20);
        return view('adverts.index', compact('category', 'region', 'categories', 'regions', 'adverts'));
    }

    /**
     * @param Advert $advert
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Advert $advert)
    {
        if (!($advert->isActive() || Gate::allows('show-advert', $advert))) {
            abort(403);
        }
        return view('adverts.show', compact('advert'));
    }

    /**
     * @param Advert $advert
     * @return string
     */
    public function phone(Advert $advert): string
    {
        if (!($advert->isActive() || Gate::allows('show-advert', $advert))) {
            abort(403);
        }
        return $advert->user->phone;
    }
}