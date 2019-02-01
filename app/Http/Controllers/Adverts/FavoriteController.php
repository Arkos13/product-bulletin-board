<?php

namespace App\Http\Controllers\Adverts;

use App\Entity\Adverts\Advert\Advert;
use App\Http\Controllers\Controller;
use App\Services\Adverts\FavoriteService;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    private $favoriteService;

    public function __construct(FavoriteService $service)
    {
        $this->favoriteService = $service;
        $this->middleware('auth');
    }

    /**
     * @param Advert $advert
     * @return \Illuminate\Http\RedirectResponse
     */
    public function add(Advert $advert)
    {
        try {
            $this->favoriteService->add(Auth::id(), $advert->id);
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
        return redirect()->route('adverts.show', $advert)->with('success', 'Advert is added to your favorites.');
    }

    /**
     * @param Advert $advert
     * @return \Illuminate\Http\RedirectResponse
     */
    public function remove(Advert $advert)
    {
        try {
            $this->favoriteService->remove(Auth::id(), $advert->id);
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
        return redirect()->route('adverts.show', $advert);
    }
}