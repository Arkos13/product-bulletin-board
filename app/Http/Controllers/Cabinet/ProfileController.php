<?php

namespace App\Http\Controllers\Cabinet;

use App\Entity\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cabinet\Profile\UpdateRequest;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();
        return view('cabinet.profile.home', compact('user'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit()
    {
        $user = Auth::user();
        return view('cabinet.profile.edit', compact('user'));
    }

    /**
     * @param UpdateRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Throwable
     */
    public function update(UpdateRequest $request)
    {
        /** @var User $user */
        $user = Auth::user();
        $oldPhone = $user->phone;
        $user->update($request->only('name', 'last_name', 'phone'));
        if ($user->phone !== $oldPhone) {
            $user->unverifyPhone();
        }
        return redirect()->route('cabinet.profile.home');
    }
}