<?php

namespace App\Http\Controllers\Admin\Adverts;

use App\Entity\Adverts\Category;
use App\Http\Requests\Admin\Adverts\Categories\CreateRequest;
use App\Http\Controllers\Controller;

class CategoryController extends Controller
{
    /**
     * CategoryController constructor.
     */
    public function __construct()
    {
        $this->middleware('can:manage-adverts-categories');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = Category::defaultOrder()->withDepth()->get();
        return view('admin.adverts.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $parents = Category::defaultOrder()->withDepth()->get();
        return view('admin.adverts.categories.create', compact('parents'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  CreateRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateRequest $request)
    {
        $category = Category::create([
            'name' => $request['name'],
            'slug' => $request['slug'],
            'parent_id' => $request['parent'],
        ]);
        return redirect()->route('admin.adverts.categories.show', $category);
    }

    /**
     * Display the specified resource.
     *
     * @param  Category $category
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        $parentAttributes = $category->parentAttributes();
        $attributes = $category->attributes()->orderBy('sort')->get();
        return view('admin.adverts.categories.show', compact('category', 'attributes', 'parentAttributes'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Category $category
     * @return \Illuminate\Http\Response
     */
    public function edit(Category $category)
    {
        $parents = Category::defaultOrder()->withDepth()->get();
        return view('admin.adverts.categories.edit', compact('category', 'parents'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  CreateRequest  $request
     * @param  Category $category
     * @return \Illuminate\Http\Response
     */
    public function update(CreateRequest $request, Category $category)
    {
        $category->update([
            'name' => $request['name'],
            'slug' => $request['slug'],
            'parent_id' => $request['parent'],
        ]);
        return redirect()->route('admin.adverts.categories.show', $category);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Category $category
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Category $category)
    {
        $category->delete();
        return redirect()->route('admin.adverts.categories.index');
    }

    /**
     * @param Category $category
     * @return \Illuminate\Http\RedirectResponse
     */
    public function first(Category $category)
    {
        if ($first = $category->siblings()->defaultOrder()->first()) {
            /** @var Category $first */
            $category->insertBeforeNode($first);
        }
        return redirect()->route('admin.adverts.categories.index');
    }

    /**
     * @param Category $category
     * @return \Illuminate\Http\RedirectResponse
     */
    public function up(Category $category)
    {
        $category->up();
        return redirect()->route('admin.adverts.categories.index');
    }

    /**
     * @param Category $category
     * @return \Illuminate\Http\RedirectResponse
     */
    public function down(Category $category)
    {
        $category->down();
        return redirect()->route('admin.adverts.categories.index');
    }

    /**
     * @param Category $category
     * @return \Illuminate\Http\RedirectResponse
     */
    public function last(Category $category)
    {
        if ($last = $category->siblings()->defaultOrder('desc')->first()) {
            /** @var Category $last */
            $category->insertAfterNode($last);
        }
        return redirect()->route('admin.adverts.categories.index');
    }

}
