<?php

namespace App\Http\Controllers\Api\Adverts;

use App\Entity\Adverts\Advert\Advert;
use App\Entity\Adverts\Category;
use App\Entity\Region;
use App\Http\Controllers\Controller;
use App\Http\Requests\Adverts\SearchRequest;
use App\Http\Resources\Adverts\AdvertDetailResource;
use App\Http\Resources\Adverts\AdvertListResource;
use App\Services\Search\SearchService;
use Illuminate\Support\Facades\Gate;

class AdvertController extends Controller
{
    private $search;

    /**
     * AdvertController constructor.
     * @param SearchService $search
     */
    public function __construct(SearchService $search)
    {
        $this->search = $search;
    }

    /**
     *  @SWG\Get(
     *     path="/adverts",
     *     tags={"Adverts"},
     *     @SWG\Response(
     *         response=200,
     *         description="Success response",
     *         @SWG\Schema(
     *             type="array",
     *             @SWG\Items(ref="#/definitions/AdvertList")
     *         ),
     *     ),
     *     security={{"Bearer": {}, "OAuth2": {}}}
     * )
     * @param SearchRequest $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(SearchRequest $request)
    {
        $region = $request->get('region') ? Region::query()->findOrFail($request->get('region')) : null;
        $category = $request->get('category') ? Category::query()->findOrFail($request->get('category')) : null;
        $result = $this->search->search($category, $region, $request, 20, $request->get('page', 1));
        return AdvertListResource::collection($result->adverts);
    }

    /**
     *  @SWG\Get(
     *     path="/adverts/{advertId}",
     *     tags={"Adverts"},
     *     @SWG\Parameter(
     *         name="advertId",
     *         description="ID of advert",
     *         in="path",
     *         required=true,
     *         type="integer"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Success response",
     *         @SWG\Schema(ref="#/definitions/AdvertDetail"),
     *     ),
     *     security={{"Bearer": {}, "OAuth2": {}}}
     * )
     * @param Advert $advert
     * @return AdvertDetailResource
     */
    public function show(Advert $advert)
    {
        if (!($advert->isActive() || Gate::allows('show-advert', $advert))) {
            abort(403);
        }
        return new AdvertDetailResource($advert);
    }
}