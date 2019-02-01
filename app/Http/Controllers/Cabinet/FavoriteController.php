<?php

namespace App\Http\Controllers\Cabinet;

use App\Entity\Adverts\Advert\Advert;
use App\Http\Controllers\Controller;
use App\Services\Adverts\FavoriteService;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    /**
     * @var FavoriteService
    */
    private $favoriteService;

    /**
     * FavoriteController constructor.
     * @param FavoriteService $favoriteService
     */
    public function __construct(FavoriteService $favoriteService)
    {
        $this->favoriteService = $favoriteService;
        $this->middleware('auth');
    }

    public function index()
    {
        $adverts = Advert::favoredByUser(Auth::user())->orderByDesc('id')->paginate(20);
        return view('cabinet.favorites.index', compact('adverts'));
    }

    public function remove(Advert $advert)
    {
        try {
            $this->favoriteService->remove(Auth::id(), $advert->id);
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
        return redirect()->route('cabinet.favorites.index');
    }
}