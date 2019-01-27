<?php

namespace App\Http\Controllers\Cabinet\Adverts;

use App\Http\Controllers\Controller;
use App\Http\Middleware\FilledProfile;

class AdvertController extends Controller
{
    public function __construct()
    {
        $this->middleware(FilledProfile::class);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('cabinet.adverts.index');
    }
}