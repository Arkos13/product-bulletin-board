<?php

namespace App\Repositories;

use App\Entity\Banner\Banner;
use App\Entity\Ticket\Ticket;
use Prettus\Repository\Eloquent\BaseRepository;

class TicketRepository extends BaseRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    function model()
    {
        return Ticket::class;
    }

    /**
     * @param array $filters
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public static function getTickets(array $filters)
    {
        $query = Ticket::query()->orderByDesc('updated_at');
        if (!empty($filters['id'])) {
            $query->where('id', $filters['id']);
        }
        if (!empty($filters['user'])) {
            $query->where('user_id', $filters['user']);
        }
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        return $query->paginate(20);
    }
}