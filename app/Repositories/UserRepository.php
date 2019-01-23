<?php

namespace App\Repositories;

use App\Entity\User;
use Illuminate\Database\Query\Builder;
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
            'role' => User::ROLE_USER,
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
            'role' => User::ROLE_USER,
            'status' => User::STATUS_ACTIVE,
        ]);
    }

    /**
     * @param array $filters
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public static function getUsers(array $filters)
    {
        /** @var Builder $query*/
        $query = User::query()->orderByDesc('id');

        if (!empty($filters['id'])) {
            $query->where('id', $filters['id']);
        }

        if (!empty($filters['name'])) {
            $query->where('name', 'like', '%' . $filters['name'] . '%');
        }
        if (!empty($filters['email'])) {
            $query->where('email', 'like', '%' . $filters['email'] . '%');
        }
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (!empty($filters['role'])) {
            $query->where('role', $filters['role']);
        }

        return $query->paginate(20);
    }
}