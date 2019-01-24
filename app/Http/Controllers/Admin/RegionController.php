<?php

namespace App\Http\Controllers\Admin;

use App\Entity\Region;
use App\Http\Requests\Admin\Regions\CreateRequest;
use App\Http\Requests\Admin\Regions\UpdateRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RegionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $regions = Region::query()->where('parent_id', null)->orderBy('name')->get();
        return view('admin.regions.index', compact('regions'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $parent = null;
        if ($request->get('parent')) {
            $parent = Region::query()->findOrFail($request->get('parent'));
        }
        return view('admin.regions.create', compact('parent'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  CreateRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateRequest $request)
    {
        $region = Region::query()->create([
            'name' => $request['name'],
            'slug' => $request['slug'],
            'parent_id' => $request['parent'],
        ]);
        return redirect()->route('admin.regions.show', $region);
    }

    /**
     * Display the specified resource.
     *
     * @param  Region $region
     * @return \Illuminate\Http\Response
     */
    public function show(Region $region)
    {
        $regions = Region::query()->where('parent_id', $region->id)->orderBy('name')->get();
        return view('admin.regions.show', compact('region', 'regions'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Region $region
     * @return \Illuminate\Http\Response
     */
    public function edit(Region $region)
    {
        return view('admin.regions.edit', compact('region'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateRequest  $request
     * @param  Region $region
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, Region $region)
    {
        $region->update([
            'name' => $request['name'],
            'slug' => $request['slug'],
        ]);
        return redirect()->route('admin.regions.show', $region);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Region $region
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Region $region)
    {
        $region->delete();
        return redirect()->route('admin.regions.index');
    }
}
