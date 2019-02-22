<?php

namespace App\Http\Controllers\Api\User;

use App\Entity\Adverts\Advert\Advert;
use App\Entity\Adverts\Category;
use App\Entity\Region;
use App\Http\Controllers\Controller;
use App\Http\Requests\Adverts\AttributesRequest;
use App\Http\Requests\Adverts\CreateRequest;
use App\Http\Requests\Adverts\UpdateRequest;
use App\Http\Requests\Adverts\PhotoRequest;
use App\Http\Resources\Adverts\AdvertDetailResource;
use App\Http\Resources\Adverts\AdvertListResource;
use App\Services\Adverts\AdvertService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class AdvertController extends Controller
{
    private $service;

    /**
     * AdvertController constructor.
     * @param AdvertService $service
     */
    public function __construct(AdvertService $service)
    {
        $this->service = $service;
    }

    /**
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        $adverts = Advert::forUser(Auth::user())->orderByDesc('id')->paginate(20);
        return AdvertListResource::collection($adverts);
    }

    /**
     * @param Advert $advert
     * @return AdvertDetailResource
     */
    public function show(Advert $advert)
    {
        $this->checkAccess($advert);
        return new AdvertDetailResource($advert);
    }

    /**
     * @param CreateRequest $request
     * @param Category $category
     * @param Region|null $region
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateRequest $request, Category $category, Region $region = null)
    {
        $advert = $this->service->create(
            Auth::id(),
            $category->id,
            $region ? $region->id : null,
            $request
        );
        return (new AdvertDetailResource($advert))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * @param UpdateRequest $request
     * @param Advert $advert
     * @return AdvertDetailResource
     */
    public function update(UpdateRequest $request, Advert $advert)
    {
        $this->checkAccess($advert);
        $this->service->edit($advert->id, $request);
        return new AdvertDetailResource(Advert::query()->findOrFail($advert->id));
    }

    /**
     * @param AttributesRequest $request
     * @param Advert $advert
     * @return AdvertDetailResource
     */
    public function attributes(AttributesRequest $request, Advert $advert)
    {
        $this->checkAccess($advert);
        $this->service->editAttributes($advert->id, $request);
        return new AdvertDetailResource(Advert::query()->findOrFail($advert->id));
    }

    /**
     * @param PhotoRequest $request
     * @param Advert $advert
     * @return AdvertDetailResource
     */
    public function photos(PhotoRequest $request, Advert $advert)
    {
        $this->checkAccess($advert);
        $this->service->addPhotos($advert->id, $request);
        return new AdvertDetailResource(Advert::query()->findOrFail($advert->id));
    }

    /**
     * @param Advert $advert
     * @return AdvertDetailResource
     */
    public function send(Advert $advert)
    {
        $this->checkAccess($advert);
        $this->service->sendToModeration($advert->id);
        return new AdvertDetailResource(Advert::query()->findOrFail($advert->id));
    }

    /**
     * @param Advert $advert
     * @return AdvertDetailResource
     */
    public function close(Advert $advert)
    {
        $this->checkAccess($advert);
        $this->service->close($advert->id);
        return new AdvertDetailResource(Advert::query()->findOrFail($advert->id));
    }

    /**
     * @param Advert $advert
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy(Advert $advert)
    {
        $this->checkAccess($advert);
        $this->service->remove($advert->id);
        return response()->json([], Response::HTTP_NO_CONTENT);
    }

    /**
     * @param Advert $advert
     */
    private function checkAccess(Advert $advert)
    {
        if (!Gate::allows('manage-own-advert', $advert)) {
            abort(403);
        }
    }
}