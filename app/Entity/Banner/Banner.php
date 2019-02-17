<?php

namespace App\Entity\Banner;

use App\Entity\Adverts\Category;
use App\Entity\Region;
use App\Entity\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $user_id
 * @property int $category_id
 * @property int $region_id
 * @property string $name
 * @property int $views
 * @property int $limit
 * @property int $clicks
 * @property int $cost
 * @property string $url
 * @property string $format
 * @property string $file
 * @property string $status
 * @property Carbon $published_at
 *
 * @property Region|null $region
 * @property Category $category
 *
 * @method Builder active()
 * @method Builder forUser(User $user)
 */
class Banner extends Model
{
    public const STATUS_DRAFT = 'draft';
    public const STATUS_MODERATION = 'moderation';
    public const STATUS_MODERATED = 'moderated';
    public const STATUS_ORDERED = 'ordered';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_CLOSED = 'close';

    protected $table = 'banner_banners';

    protected $guarded = ['id'];

    protected $casts = [
        'published_at' => 'datetime'
    ];

    /**
     * @return array
     */
    public static function statusesList(): array
    {
        return [
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_MODERATION => 'On Moderation',
            self::STATUS_MODERATED => 'moderated',
            self::STATUS_ORDERED => 'Payment',
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_CLOSED => 'Closed',
        ];
    }

    /**
     * @return array
     */
    public static function formatsList(): array
    {
        return [
            '240x400',
        ];
    }

    public function view()
    {
        $this->assertIsActive();
        $this->views++;
        if ($this->views >= $this->limit) {
            $this->status = self::STATUS_CLOSED;
        }
        $this->save();
    }

    public function click()
    {
        $this->assertIsActive();
        $this->clicks++;
        $this->save();
    }

    public function sendToModeration()
    {
        if (!$this->isDraft()) {
            throw new \DomainException('Advert is not draft.');
        }
        $this->update([
            'status' => self::STATUS_MODERATION,
        ]);
    }

    public function cancelModeration()
    {
        if (!$this->isOnModeration()) {
            throw new \DomainException('Advert is not sent to moderation.');
        }
        $this->update([
            'status' => self::STATUS_DRAFT,
        ]);
    }

    public function moderate(): void
    {
        if (!$this->isOnModeration()) {
            throw new \DomainException('Advert is not sent to moderation.');
        }
        $this->update([
            'status' => self::STATUS_MODERATED,
        ]);
    }

    public function reject($reason)
    {
        $this->update([
            'status' => self::STATUS_DRAFT,
            'reject_reason' => $reason,
        ]);
    }

    public function order(int $cost)
    {
        if (!$this->isModerated()) {
            throw new \DomainException('Advert is not moderated.');
        }
        $this->update([
            'cost' => $cost,
            'status' => self::STATUS_ORDERED,
        ]);
    }

    public function pay(Carbon $date)
    {
        if (!$this->isOrdered()) {
            throw new \DomainException('Advert is not ordered.');
        }
        $this->update([
            'published_at' => $date,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * @return int
     */
    public function getWidth(): int
    {
        return explode('x', $this->format)[0];
    }

    /**
     * @return int
     */
    public function getHeight(): int
    {
        return explode('x', $this->format)[1];
    }

    /**
     * @return bool
     */
    public function canBeChanged(): bool
    {
        return $this->isDraft();
    }

    /**
     * @return bool
     */
    public function canBeRemoved(): bool
    {
        return $this->isDraft();
    }

    /**
     * @return bool
     */
    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    /**
     * @return bool
     */
    public function isOnModeration(): bool
    {
        return $this->status === self::STATUS_MODERATION;
    }

    /**
     * @return bool
     */
    public function isModerated(): bool
    {
        return $this->status === self::STATUS_MODERATED;
    }

    /**
     * @return bool
     */
    public function isOrdered(): bool
    {
        return $this->status === self::STATUS_ORDERED;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /**
     * @return bool
     */
    public function isClosed(): bool
    {
        return $this->status === self::STATUS_CLOSED;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function region()
    {
        return $this->belongsTo(Region::class, 'region_id', 'id');
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeActive(Builder $query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
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

    private function assertIsActive()
    {
        if (!$this->isActive()) {
            throw new \DomainException('Banner is not active.');
        }
    }
}