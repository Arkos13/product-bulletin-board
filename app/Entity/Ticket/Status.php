<?php

namespace App\Entity\Ticket;

use App\Entity\User\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $ticket_id
 * @property int $user_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property string $status
 */
class Status extends Model
{
    public const OPEN = 'open';
    public const APPROVED = 'approved';
    public const CLOSED = 'closed';

    protected $table = 'ticket_statuses';

    protected $guarded = ['id'];

    /**
     * @return array
     */
    public static function statusesList(): array
    {
        return [
            self::OPEN => 'Open',
            self::APPROVED => 'Approved',
            self::CLOSED => 'Closed',
        ];
    }

    /**
     * @return bool
     */
    public function isOpen(): bool
    {
        return $this->status === self::OPEN;
    }

    /**
     * @return bool
     */
    public function isApproved(): bool
    {
        return $this->status === self::APPROVED;
    }

    /**
     * @return bool
     */
    public function isClosed(): bool
    {
        return $this->status === self::CLOSED;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}