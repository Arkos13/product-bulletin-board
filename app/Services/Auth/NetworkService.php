<?php

namespace App\Services\Auth;

use App\Entity\User\User;
use App\Repositories\UserRepository;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\DB;
use Laravel\Socialite\Contracts\User as NetworkUser;

class NetworkService
{
    /**
     * @param string $network
     * @param NetworkUser $data
     * @return User
     */
    public function auth(string $network, NetworkUser $data): User
    {
        if ($user = User::byNetwork($network, $data->getId())->first()) {
            /** @var User $user*/
            return $user;
        }

        if ($data->getEmail() && $user = User::query()->where('email', $data->getEmail())->exists()) {
            throw new \DomainException('User with this email is already registered.');
        }

        $user = DB::transaction(function() use ($network, $data) {
            return UserRepository::registerByNetwork($network, $data->getId());
        });

        event(new Registered($user));
        return $user;
    }
}