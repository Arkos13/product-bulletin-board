<?php

namespace App\Http\Controllers\Cabinet\Adverts;

use App\Entity\Adverts\Advert\Advert;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AdvertController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $adverts = Advert::forUser(Auth::user())->orderByDesc('id')->paginate(20);
        return view('cabinet.adverts.index', compact('adverts'));
    }
}