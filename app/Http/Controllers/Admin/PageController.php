<?php

namespace App\Http\Controllers\Admin;

use App\Entity\Page;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Pages\PageRequest;

class PageController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:manage-pages');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $pages = Page::defaultOrder()->withDepth()->get();
        return view('admin.pages.index', compact('pages'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $parents = Page::defaultOrder()->withDepth()->get();
        return view('admin.pages.create', compact('parents'));
    }

    /**
     * @param PageRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(PageRequest $request)
    {
        $page = Page::create([
            'title' => $request['title'],
            'slug' => $request['slug'],
            'menu_title' => $request['menu_title'],
            'parent_id' => $request['parent'],
            'content' => $request['content'],
            'description' => $request['description'],
        ]);
        return redirect()->route('admin.pages.show', $page);
    }

    /**
     * @param Page $page
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Page $page)
    {
        return view('admin.pages.show', compact('page'));
    }

    /**
     * @param Page $page
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Page $page)
    {
        $parents = Page::defaultOrder()->withDepth()->get();
        return view('admin.pages.edit', compact('page', 'parents'));
    }

    /**
     * @param PageRequest $request
     * @param Page $page
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(PageRequest $request, Page $page)
    {
        $page->update([
            'title' => $request['title'],
            'slug' => $request['slug'],
            'menu_title' => $request['menu_title'],
            'parent_id' => $request['parent'],
            'content' => $request['content'],
            'description' => $request['description'],
        ]);
        return redirect()->route('admin.pages.show', $page);
    }

    /**
     * @param Page $page
     * @return \Illuminate\Http\RedirectResponse
     */
    public function first(Page $page)
    {
        if ($first = $page->siblings()->defaultOrder()->first()) {
            $page->insertBeforeNode($first);
        }
        return redirect()->route('admin.pages.index');
    }

    /**
     * @param Page $page
     * @return \Illuminate\Http\RedirectResponse
     */
    public function up(Page $page)
    {
        $page->up();
        return redirect()->route('admin.pages.index');
    }

    /**
     * @param Page $page
     * @return \Illuminate\Http\RedirectResponse
     */
    public function down(Page $page)
    {
        $page->down();
        return redirect()->route('admin.pages.index');
    }

    /**
     * @param Page $page
     * @return \Illuminate\Http\RedirectResponse
     */
    public function last(Page $page)
    {
        if ($last = $page->siblings()->defaultOrder('desc')->first()) {
            $page->insertAfterNode($last);
        }
        return redirect()->route('admin.pages.index');
    }

    /**
     * @param Page $page
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy(Page $page)
    {
        $page->delete();
        return redirect()->route('admin.pages.index');
    }
}