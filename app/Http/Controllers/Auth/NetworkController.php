<?php

namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use App\Services\Auth\NetworkService;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class NetworkController extends Controller
{
    private $service;

    /**
     * NetworkController constructor.
     * @param NetworkService $service
     */
    public function __construct(NetworkService $service)
    {
        $this->service = $service;
    }

    /**
     * @param string $network
     * @return mixed
     */
    public function redirect(string $network)
    {
        return Socialite::driver($network)->user();
    }

    /**
     * @param string $network
     * @return \Illuminate\Http\RedirectResponse
     */
    public function callback(string $network)
    {
        $data = Socialite::driver($network)->user();
        try {
            $user = $this->service->auth($network, $data);
            Auth::login($user);
            return redirect()->intended();
        } catch (\DomainException $e) {
            return redirect()->route('login')->with('error', $e->getMessage());
        }
    }
}