<?php

namespace App\Http\Controllers;

use App\Http\Router\PagePath;

class PageController extends Controller
{
    /**
     * @param PagePath $path
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(PagePath $path)
    {
        $page = $path->page;
        return view('page', compact('page'));
    }
}