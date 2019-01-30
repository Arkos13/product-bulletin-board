<?php

namespace App\Http\Controllers\Cabinet\Adverts;

use App\Entity\Adverts\Category;
use App\Entity\Region;
use App\Http\Controllers\Controller;
use App\Http\Requests\Adverts\CreateRequest;
use App\Services\Adverts\AdvertService;
use Illuminate\Support\Facades\Auth;

class CreateController extends Controller
{
    /**
     * @var AdvertService
     */
    private $advertService;

    /**
     * @param AdvertService $advertService
     */
    public function __construct(AdvertService $advertService)
    {
        $this->advertService = $advertService;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function category()
    {
        $categories = Category::defaultOrder()->withDepth()->get()->toTree();
        return view('cabinet.adverts.create.category', compact('categories'));
    }

    /**
     * @param Category $category
     * @param Region|null $region
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function region(Category $category, Region $region = null)
    {
        $regions = Region::query()->where('parent_id', $region ? $region->id : null)->orderBy('name')->get();
        return view('cabinet.adverts.create.region', compact('category', 'region', 'regions'));
    }

    /**
     * @param Category $category
     * @param Region|null $region
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function advert(Category $category, Region $region = null)
    {
        return view('cabinet.adverts.create.advert', compact('category', 'region'));
    }

    /**
     * @param CreateRequest $request
     * @param Category $category
     * @param Region|null $region
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(CreateRequest $request, Category $category, Region $region = null)
    {
        try {
            $advert = $this->advertService->create(
                Auth::id(),
                $category->id,
                $region ? $region->id : null,
                $request
            );
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
        return redirect()->route('adverts.show', $advert);
    }
}