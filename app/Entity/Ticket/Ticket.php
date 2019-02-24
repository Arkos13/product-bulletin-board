<?php

namespace App\Entity\Ticket;

use App\Entity\User\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $user_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property string $subject
 * @property string $content
 * @property string $status
 *
 * @method Builder forUser(User $user)
 */
class Ticket extends Model
{
    protected $table = 'ticket_tickets';

    protected $guarded = ['id'];

    /**
     * @param int $userId
     * @param string $subject
     * @param string $content
     * @return Ticket
     */
    public static function new(int $userId, string $subject, string $content): self
    {
        /** @var Ticket $ticket*/
        $ticket = self::query()->create([
            'user_id' => $userId,
            'subject' => $subject,
            'content' => $content,
            'status' => Status::OPEN,
        ]);
        $ticket->setStatus(Status::OPEN, $userId);
        return $ticket;
    }

    /**
     * @param string $subject
     * @param string $content
     */
    public function edit(string $subject, string $content): void
    {
        $this->update([
            'subject' => $subject,
            'content' => $content,
        ]);
    }

    /**
     * @param int $userId
     * @param $message
     */
    public function addMessage(int $userId, $message): void
    {
        if (!$this->allowsMessages()) {
            throw new \DomainException('Ticket is closed for messages.');
        }
        $this->messages()->create([
            'user_id' => $userId,
            'message' => $message,
        ]);
        $this->update();
    }

    /**
     * @return bool
     */
    public function allowsMessages(): bool
    {
        return !$this->isClosed();
    }

    /**
     * @param int $userId
     */
    public function approve(int $userId): void
    {
        if ($this->isApproved()) {
            throw new \DomainException('Ticket is already approved.');
        }
        $this->setStatus(Status::APPROVED, $userId);
    }

    /**
     * @param int $userId
     */
    public function close(int $userId): void
    {
        if ($this->isClosed()) {
            throw new \DomainException('Ticket is already closed.');
        }
        $this->setStatus(Status::CLOSED, $userId);
    }

    /**
     * @param int $userId
     */
    public function reopen(int $userId): void
    {
        if (!$this->isClosed()) {
            throw new \DomainException('Ticket is not closed.');
        }
        $this->setStatus(Status::APPROVED, $userId);
    }

    /**
     * @return bool
     */
    public function isOpen(): bool
    {
        return $this->status === Status::OPEN;
    }

    /**
     * @return bool
     */
    public function isApproved(): bool
    {
        return $this->status === Status::APPROVED;
    }

    /**
     * @return bool
     */
    public function isClosed(): bool
    {
        return $this->status === Status::CLOSED;
    }

    /**
     * @return bool
     */
    public function canBeRemoved(): bool
    {
        return $this->isOpen();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function messages()
    {
        return $this->hasMany(Message::class, 'ticket_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function statuses()
    {
        return $this->hasMany(Status::class, 'ticket_id', 'id');
    }

    /**
     * @param $status
     * @param int|null $userId
     */
    private function setStatus($status, ?int $userId)
    {
        $this->statuses()->create(['status' => $status, 'user_id' => $userId]);
        $this->update(['status' => $status]);
    }

    /**
     * @param Builder $query
     * @param User $user
     * @return Builder
     */
    public function scopeForUser(Builder $query, User $user)
    {
        return $query->where('user_id', $user->id);
    }
}