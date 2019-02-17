<?php

namespace App\Repositories;

use App\Entity\Banner\Banner;
use Prettus\Repository\Eloquent\BaseRepository;

class BannerRepository extends BaseRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    function model()
    {
        return Banner::class;
    }

    /**
     * @param array $filters
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public static function getBanners(array $filters)
    {
        $query = Banner::query()->orderByDesc('updated_at');
        if (!empty($filters['id'])) {
            $query->where('id', $filters['id']);
        }
        if (!empty($filters['user'])) {
            $query->where('user_id', $filters['user']);
        }
        if (!empty($filters['region'])) {
            $query->where('region_id', $filters['region']);
        }
        if (!empty($filters['category'])) {
            $query->where('category_id',  $filters['category']);
        }
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        return $query->paginate(20);
    }
}