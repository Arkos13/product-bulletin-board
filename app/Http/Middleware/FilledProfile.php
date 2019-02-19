<?php
namespace App\Http\Middleware;

use App\Entity\User\User;
use Illuminate\Support\Facades\Auth;

class FilledProfile
{
    /**
     * @param $request
     * @param \Closure $next
     * @return \Illuminate\Http\RedirectResponse|mixed
     */
    public function handle($request, \Closure $next)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user->hasFilledProfile()) {
            return redirect()
                ->route('cabinet.profile.home')
                ->with('error', 'Please fill your profile and verify your phone.');
        }
        return $next($request);
    }
}