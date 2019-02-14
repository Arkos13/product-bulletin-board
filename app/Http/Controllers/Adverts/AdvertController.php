<?php

namespace App\Http\Controllers\Adverts;

use App\Entity\Adverts\Advert\Advert;
use App\Entity\Adverts\Category;
use App\Entity\Region;
use App\Http\Controllers\Controller;
use App\Http\Requests\Adverts\SearchRequest;
use App\Http\Router\AdvertsPath;
use App\Services\Search\SearchService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class AdvertController extends Controller
{
    /**
     * @var SearchService
    */
    private $search;

    /**
     * @param SearchService $search
     */
    public function __construct(SearchService $search)
    {
        $this->search = $search;
    }

    /**
     * @param SearchRequest $request
     * @param AdvertsPath $path
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(SearchRequest $request, AdvertsPath $path)
    {
        $region = $path->region;
        $category = $path->category;
        $result = $this->search->search($category, $region, $request, 20, $request->get('page', 1));
        $adverts = $result->adverts;
        $regionsCounts = $result->regionsCounts;
        $categoriesCounts = $result->categoriesCounts;
        $query = $region ? $region->children() : Region::roots();
        $regions = $query->orderBy('name')->getModels();
        $query = $category ? $category->children() : Category::whereIsRoot();
        $categories = $query->defaultOrder()->getModels();
        $regions = array_filter($regions, function (Region $region) use ($regionsCounts) {
            return isset($regionsCounts[$region->id]) && $regionsCounts[$region->id] > 0;
        });
        $categories = array_filter($categories, function (Category $category) use ($categoriesCounts) {
            return isset($categoriesCounts[$category->id]) && $categoriesCounts[$category->id] > 0;
        });
        return view('adverts.index', compact(
            'category', 'region',
            'categories', 'regions',
            'regionsCounts', 'categoriesCounts',
            'adverts'
        ));
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
        $user = Auth::user();
        return view('adverts.show', compact('advert', 'user'));
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