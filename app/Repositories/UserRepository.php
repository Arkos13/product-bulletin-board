<?php

namespace App\Repositories;

use App\Entity\User;
use Illuminate\Support\Str;
use Prettus\Repository\Eloquent\BaseRepository;

class UserRepository extends BaseRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    function model()
    {
        return User::class;
    }

    /**
     * @param string $name
     * @param string $email
     * @param string $password
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model
     */
    public static function register(string $name, string $email, string $password)
    {
        return User::query()->create([
            'name' => $name,
            'email' => $email,
            'password' => bcrypt($password),
            'verify_token' => Str::uuid(),
            'status' => User::STATUS_WAIT,
        ]);
    }

    /**
     * @param $name
     * @param $email
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model
     */
    public static function new($name, $email)
    {
        return User::query()->create([
            'name' => $name,
            'email' => $email,
            'password' => bcrypt(Str::random()),
            'status' => User::STATUS_ACTIVE,
        ]);
    }

}