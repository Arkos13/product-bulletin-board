<?php

namespace App\Http\Controllers\Admin\Adverts;

use App\Entity\Adverts\Attribute;
use App\Entity\Adverts\Category;
use App\Http\Requests\Admin\Adverts\Attributes\CreateUpdateRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AttributeController extends Controller
{

    /**
     * AttributeController constructor.
     */
    public function __construct()
    {
        $this->middleware('can:manage-adverts-categories');
    }

    /**
     * Show the form for creating a new resource.
     * @param Category $category
     * @return \Illuminate\Http\Response
     */
    public function create(Category $category)
    {
        $types = Attribute::typesList();
        return view('admin.adverts.categories.attributes.create', compact('category', 'types'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  CreateUpdateRequest $request
     * @param Category $category
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(CreateUpdateRequest $request, Category $category)
    {
        $attribute = $category->attributes()->create([
            'name' => $request['name'],
            'type' => $request['type'],
            'required' => (bool)$request['required'],
            'variants' => array_map('trim', preg_split('#[\r\n]+#', $request['variants'])),
            'sort' => $request['sort'],
        ]);
        return redirect()->route('admin.adverts.categories.attributes.show', [$category, $attribute]);
    }

    /**
     * Display the specified resource.
     *
     * @param Category $category
     * @param Attribute $attribute
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Category $category, Attribute $attribute)
    {
        return view('admin.adverts.categories.attributes.show', compact('category', 'attribute'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Category $category
     * @param Attribute $attribute
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Category $category, Attribute $attribute)
    {
        $types = Attribute::typesList();
        return view('admin.adverts.categories.attributes.edit', compact('category', 'attribute', 'types'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  CreateUpdateRequest $request
     * @param Category $category
     * @param Attribute $attribute
     * @return \Illuminate\Http\Response
     */
    public function update(CreateUpdateRequest $request, Category $category, Attribute $attribute)
    {
        $category->attributes()->findOrFail($attribute->id)->update([
            'name' => $request['name'],
            'type' => $request['type'],
            'required' => (bool)$request['required'],
            'variants' => array_map('trim', preg_split('#[\r\n]+#', $request['variants'])),
            'sort' => $request['sort']
        ]);
        return redirect()->route('admin.adverts.categories.show', $category);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Category $category
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Category $category)
    {
        $category->delete();
        return redirect()->route('admin.adverts.categories.show', $category);
    }
}
