<?php

namespace App\Http\Controllers\Cabinet;

use App\Entity\User\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\PhoneVerifyRequest;
use App\Services\Profile\PhoneService;
use App\Services\Sms\SmsSender;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PhoneController extends Controller
{
    /**
     * @var PhoneService
    */
    private $service;

    /**
     * PhoneController constructor.
     * @param PhoneService $service
     */
    public function __construct(PhoneService $service)
    {
        $this->service = $service;
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Throwable
     */
    public function request()
    {
        try {
            $this->service->request(Auth::id());
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
        return redirect()->route('cabinet.profile.phone');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function form()
    {
        $user = Auth::user();
        return view('cabinet.profile.phone', compact('user'));
    }

    /**
     * @param PhoneVerifyRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Throwable
     */
    public function verify(PhoneVerifyRequest $request)
    {
        try {
            $this->service->verify(Auth::id(), $request);
        } catch (\DomainException $e) {
            return redirect()->route('cabinet.profile.phone')->with('error', $e->getMessage());
        }
        return redirect()->route('cabinet.profile.home');
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Throwable
     */
    public function auth()
    {
        $this->service->toggleAuth(Auth::id());
        return redirect()->route('cabinet.profile.home');
    }
}