<?php

namespace App\Http\Controllers\Admin\Adverts;

use App\Entity\Adverts\Advert\Advert;
use App\Entity\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\Adverts\PhotoRequest;
use App\Http\Requests\Adverts\RejectRequest;
use App\Http\Requests\Adverts\AttributesRequest;
use App\Http\Requests\Adverts\UpdateRequest;
use App\Repositories\AdvertRepository;
use App\Services\Adverts\AdvertService;
use Illuminate\Http\Request;

class AdvertController extends Controller
{
    /**
     * @var AdvertService
    */
    private $advertService;

    /**
     * AdvertController constructor.
     * @param AdvertService $advertService
     */
    public function __construct(AdvertService $advertService)
    {
        $this->advertService = $advertService;
        $this->middleware('can:manage-adverts');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $adverts = AdvertRepository::getAdverts($request->all());
        $statuses = Advert::statusesList();
        $roles = User::rolesList();
        return view('admin.adverts.adverts.index', compact('adverts', 'statuses', 'roles'));
    }

    /**
     * @param Advert $advert
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function editForm(Advert $advert)
    {
        return view('adverts.edit.advert', compact('advert'));
    }

    /**
     * @param UpdateRequest $request
     * @param Advert $advert
     * @return \Illuminate\Http\RedirectResponse
     */
    public function edit(UpdateRequest $request, Advert $advert)
    {
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
        return view('adverts.edit.attributes', compact('advert'));
    }

    /**
     * @param AttributesRequest $request
     * @param Advert $advert
     * @return \Illuminate\Http\RedirectResponse
     */
    public function attributes(AttributesRequest $request, Advert $advert)
    {
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
        return view('adverts.edit.photos', compact('advert'));
    }

    /**
     * @param PhotoRequest $request
     * @param Advert $advert
     * @return \Illuminate\Http\RedirectResponse
     */
    public function photos(PhotoRequest $request, Advert $advert)
    {
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
    public function moderate(Advert $advert)
    {
        try {
            $this->advertService->moderate($advert->id);
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
        return redirect()->route('adverts.show', $advert);
    }

    /**
     * @param Advert $advert
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function rejectForm(Advert $advert)
    {
        return view('admin.adverts.adverts.reject', compact('advert'));
    }

    /**
     * @param RejectRequest $request
     * @param Advert $advert
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reject(RejectRequest $request, Advert $advert)
    {
        try {
            $this->advertService->reject($advert->id, $request);
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
        try {
            $this->advertService->remove($advert->id);
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
        return redirect()->route('admin.adverts.adverts.index');
    }
}