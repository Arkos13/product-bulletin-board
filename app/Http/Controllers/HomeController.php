<?php

namespace App\Http\Controllers;

use App\Entity\Adverts\Category;
use App\Entity\Region;

class HomeController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $regions = Region::roots()->orderBy('name')->getModels();
        $categories = Category::whereIsRoot()->defaultOrder()->getModels();
        return view('home', compact('regions', 'categories'));
    }
}
