<?php

namespace App\Http\Controllers\Admin;

use App\Entity\Banner\Banner;
use App\Entity\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\Banner\EditRequest;
use App\Http\Requests\Banner\RejectRequest;
use App\Services\Banner\BannerService;
use Illuminate\Http\Request;
use App\Repositories\BannerRepository;

class BannerController extends Controller
{
    /**
     * @var BannerService
    */
    private $bannerService;

    /**
     * BannerController constructor.
     * @param BannerService $bannerService
     */
    public function __construct(BannerService $bannerService)
    {
        $this->bannerService = $bannerService;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $banners = BannerRepository::getBanners($request->all());
        $statuses = Banner::statusesList();
        return view('admin.banners.index', compact('banners', 'statuses'));
    }

    /**
     * @param Banner $banner
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Banner $banner)
    {
        return view('admin.banners.show', compact('banner'));
    }

    /**
     * @param Banner $banner
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function editForm(Banner $banner)
    {
        return view('admin.banners.edit', compact('banner'));
    }

    /**
     * @param EditRequest $request
     * @param Banner $banner
     * @return \Illuminate\Http\RedirectResponse
     */
    public function edit(EditRequest $request, Banner $banner)
    {
        try {
            $this->bannerService->editByAdmin($banner->id, $request);
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('admin.banners.show', $banner);
    }

    /**
     * @param Banner $banner
     * @return \Illuminate\Http\RedirectResponse
     */
    public function moderate(Banner $banner)
    {
        try {
            $this->bannerService->moderate($banner->id);
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
        return redirect()->route('admin.banners.show', $banner);
    }

    /**
     * @param Banner $banner
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function rejectForm(Banner $banner)
    {
        return view('admin.banners.reject', compact('banner'));
    }

    /**
     * @param RejectRequest $request
     * @param Banner $banner
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reject(RejectRequest $request, Banner $banner)
    {
        try {
            $this->bannerService->reject($banner->id, $request);
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
        return redirect()->route('admin.banners.show', $banner);
    }

    /**
     * @param Banner $banner
     * @return \Illuminate\Http\RedirectResponse
     */
    public function pay(Banner $banner)
    {
        try {
            $this->bannerService->pay($banner->id);
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
        return redirect()->route('admin.banners.show', $banner);
    }

    /**
     * @param Banner $banner
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy(Banner $banner)
    {
        try {
            $this->bannerService->removeByAdmin($banner->id);
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
        return redirect()->route('admin.banners.index');
    }
}