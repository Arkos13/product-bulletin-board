<?php

namespace App\Entity\User;

use App\Entity\Adverts\Advert\Advert;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Carbon\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property string $last_name
 * @property string $email
 * @property string $phone
 * @property bool $phone_verified
 * @property string $password
 * @property string $verify_token
 * @property string $phone_verify_token
 * @property Carbon $phone_verify_token_expire
 * @property string $role
 * @property string $status
 * @property boolean $phone_auth
 * @property Network[] networks
 * @method Builder byNetwork(string $network, string $identity)
 */
class User extends Authenticatable
{
    use Notifiable;

    public const STATUS_WAIT = 'wait';
    public const STATUS_ACTIVE = 'active';

    public const ROLE_USER = 'user';
    public const ROLE_ADMIN = 'admin';
    public const ROLE_MODERATOR = 'moderator';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'last_name', 'email', 'phone', 'password', 'verify_token', 'status', 'role'
    ];

    protected $casts = [
        'phone_verified' => 'boolean',
        'phone_verify_token_expire' => 'datetime',
        'phone_auth' => 'boolean'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * @return array
     */
    public static function rolesList(): array
    {
        return [
            self::ROLE_USER => 'User',
            self::ROLE_ADMIN => 'Admin',
            self::ROLE_MODERATOR => 'Moderator'
        ];
    }

    /**
     * @return bool
     */
    public function isWait(): bool
    {
        return $this->status === self::STATUS_WAIT;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function verify(): void
    {
        if (!$this->isWait()) {
            throw new \DomainException('User is already verified.');
        }

        $this->update([
            'status' => self::STATUS_ACTIVE,
            'verify_token' => null,
        ]);
    }

    /**
     * @param string $role
     */
    public function changeRole(string $role): void
    {
        if (!in_array($role, self::rolesList(), true)) {
            throw new \InvalidArgumentException("Undefined role {$role}");
        }

        if ($this->role === $role) {
            throw new \DomainException("Role is already assigned.");
        }

        $this->update(['role' => $role]);
    }

    /**
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    /**
     * @return bool
     */
    public function isModerator(): bool
    {
        return $this->role === self::ROLE_MODERATOR;
    }

    /**
     * @throws \Throwable
     */
    public function unverifyPhone()
    {
        $this->phone_verified = false;
        $this->phone_verify_token = null;
        $this->phone_verify_token_expire = null;
        $this->phone_auth = false;
        $this->saveOrFail();
    }

    /**
     * @param Carbon $now
     * @return string
     * @throws \Throwable
     */
    public function requestPhoneVerification(Carbon $now)
    {
        if (empty($this->phone)) {
            throw new \DomainException('Phone number is empty.');
        }
        if (!empty($this->phone_verify_token) && $this->phone_verify_token_expire && $this->phone_verify_token_expire->gt($now)) {
            throw new \DomainException('Token is already requested.');
        }
        $this->phone_verified = false;
        $this->phone_verify_token = (string)random_int(10000, 99999);
        $this->phone_verify_token_expire = $now->copy()->addSeconds(300);
        $this->saveOrFail();
        return $this->phone_verify_token;
    }

    /**
     * @param string $token
     * @param Carbon $now
     * @throws \Throwable
     */
    public function verifyPhone(string $token, Carbon $now): void
    {
        if ($token !== $this->phone_verify_token) {
            throw new \DomainException('Incorrect verify token.');
        }
        if ($this->phone_verify_token_expire->lt($now)) {
            throw new \DomainException('Token is expired.');
        }
        $this->phone_verified = true;
        $this->phone_verify_token = null;
        $this->phone_verify_token_expire = null;
        $this->saveOrFail();
    }

    /**
     * @return bool
     */
    public function isPhoneVerified(): bool
    {
        return $this->phone_verified;
    }

    /**
     * @throws \Throwable
     */
    public function enablePhoneAuth()
    {
        if (!empty($this->phone) && !$this->isPhoneVerified()) {
            throw new \DomainException('Phone number is not verified.');
        }
        $this->phone_auth = true;
        $this->saveOrFail();
    }

    /**
     * @throws \Throwable
     */
    public function disablePhoneAuth()
    {
        $this->phone_auth = false;
        $this->saveOrFail();
    }

    /**
     * @return bool
     */
    public function isPhoneAuthEnabled(): bool
    {
        return (bool) $this->phone_auth;
    }

    /**
     * @return bool
     */
    public function hasFilledProfile(): bool
    {
        return !empty($this->name) && !empty($this->last_name) && $this->isPhoneVerified();
    }

    /**
     * @param int $id
     */
    public function addToFavorites(int $id)
    {
        if ($this->hasInFavorites($id)) {
            throw new \DomainException('This advert is already added to favorites.');
        }
        $this->favorites()->attach($id);
    }

    /**
     * @param int $id
     */
    public function removeFromFavorites(int $id)
    {
        $this->favorites()->detach($id);
    }

    /**
     * @param int $id
     * @return bool
     */
    public function hasInFavorites(int $id): bool
    {
        return $this->favorites()->where('id', $id)->exists();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function favorites()
    {
        return $this->belongsToMany(Advert::class, 'advert_favorites', 'user_id', 'advert_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function networks()
    {
        return $this->hasMany(Network::class, 'user_id', 'id');
    }

    /**
     * @param Builder $query
     * @param string $network
     * @param string $identity
     * @return Builder
     */
    public function scopeByNetwork(Builder $query, string $network, string $identity): Builder
    {
        return $query->whereHas('networks', function(Builder $query) use ($network, $identity) {
            $query->where('network', $network)->where('identity', $identity);
        });
    }
}
