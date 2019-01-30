<?php

namespace App\Http\Controllers\Cabinet\Adverts;

use App\Entity\Adverts\Advert\Advert;
use App\Http\Controllers\Controller;
use App\Http\Requests\Adverts\AttributesRequest;
use App\Http\Requests\Adverts\UpdateRequest;
use App\Http\Requests\Adverts\PhotoRequest;
use App\Services\Adverts\AdvertService;
use Illuminate\Support\Facades\Gate;

class ManageController extends Controller
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
     * @param Advert $advert
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function editForm(Advert $advert)
    {
        $this->checkAccess($advert);
        return view('adverts.edit.advert', compact('advert'));
    }

    /**
     * @param UpdateRequest $request
     * @param Advert $advert
     * @return \Illuminate\Http\RedirectResponse
     */
    public function edit(UpdateRequest $request, Advert $advert)
    {
        $this->checkAccess($advert);
        try {
            $this->advertService->edit($advert->id, $request);
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
        return redirect()->route('adverts.show', $advert);
    }

    /**
     * @param Advert $advert
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function attributesForm(Advert $advert)
    {
        $this->checkAccess($advert);
        return view('adverts.edit.attributes', compact('advert'));
    }

    /**
     * @param AttributesRequest $request
     * @param Advert $advert
     * @return \Illuminate\Http\RedirectResponse
     */
    public function attributes(AttributesRequest $request, Advert $advert)
    {
        $this->checkAccess($advert);
        try {
            $this->advertService->editAttributes($advert->id, $request);
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
        return redirect()->route('adverts.show', $advert);
    }

    /**
     * @param Advert $advert
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function photosForm(Advert $advert)
    {
        $this->checkAccess($advert);
        return view('adverts.edit.photos', compact('advert'));
    }

    /**
     * @param PhotoRequest $request
     * @param Advert $advert
     * @return \Illuminate\Http\RedirectResponse
     */
    public function photos(PhotoRequest $request, Advert $advert)
    {
        $this->checkAccess($advert);
        try {
            $this->advertService->addPhotos($advert->id, $request);
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
        return redirect()->route('adverts.show', $advert);
    }

    /**
     * @param Advert $advert
     * @return \Illuminate\Http\RedirectResponse
     */
    public function send(Advert $advert)
    {
        $this->checkAccess($advert);
        try {
            $this->advertService->sendToModeration($advert->id);
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
        return redirect()->route('adverts.show', $advert);
    }

    /**
     * @param Advert $advert
     * @return \Illuminate\Http\RedirectResponse
     */
    public function close(Advert $advert)
    {
        $this->checkAccess($advert);
        try {
            $this->advertService->close($advert->id);
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
        return redirect()->route('adverts.show', $advert);
    }

    /**
     * @param Advert $advert
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy(Advert $advert)
    {
        $this->checkAccess($advert);
        try {
            $this->advertService->remove($advert->id);
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
        return redirect()->route('cabinet.adverts.index');
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