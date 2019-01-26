<?php

namespace Tests\Unit\Entity\User;

use App\Entity\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PhoneTest extends TestCase
{
    use DatabaseTransactions;

    public function testDefault(): void
    {
        $user = factory(User::class)->create([
            'phone' => null,
            'phone_verified' => false,
            'phone_verify_token' => null,
        ]);
        self::assertFalse($user->isPhoneVerified());
    }

    /**
     * @throws \Throwable
     */
    public function testRequestEmptyPhone(): void
    {
        /** @var User $user */
        $user = factory(User::class)->create([
            'phone' => null,
            'phone_verified' => false,
            'phone_verify_token' => null,
        ]);
        $this->expectExceptionMessage('Phone number is empty.');
        $user->requestPhoneVerification(Carbon::now());
    }

    /**
     * @throws \Throwable
     */
    public function testRequest(): void
    {
        /** @var User $user */
        $user = factory(User::class)->create([
            'phone' => '79000000000',
            'phone_verified' => false,
            'phone_verify_token' => null,
        ]);
        $token = $user->requestPhoneVerification(Carbon::now());
        self::assertFalse($user->isPhoneVerified());
        self::assertNotEmpty($token);
    }

    /**
     * @throws \Throwable
     */
    public function testRequestWithOldPhone(): void
    {
        /** @var User $user */
        $user = factory(User::class)->create([
            'phone' => '79000000000',
            'phone_verified' => true,
            'phone_verify_token' => null,
        ]);
        self::assertTrue($user->isPhoneVerified());
        $user->requestPhoneVerification(Carbon::now());
        self::assertFalse($user->isPhoneVerified());
        self::assertNotEmpty($user->phone_verify_token);
    }

    /**
     * @throws \Throwable
     */
    public function testRequestAlreadySentTimeout(): void
    {
        /** @var User $user */
        $user = factory(User::class)->create([
            'phone' => '79000000000',
            'phone_verified' => true,
            'phone_verify_token' => null,
        ]);
        $user->requestPhoneVerification($now = Carbon::now());
        $user->requestPhoneVerification($now->copy()->addSeconds(500));
        self::assertFalse($user->isPhoneVerified());
    }

    /**
     * @throws \Throwable
     */
    public function testRequestAlreadySent(): void
    {
        /** @var User $user */
        $user = factory(User::class)->create([
            'phone' => '79000000000',
            'phone_verified' => true,
            'phone_verify_token' => null,
        ]);
        $user->requestPhoneVerification($now = Carbon::now());
        $this->expectExceptionMessage('Token is already requested.');
        $user->requestPhoneVerification($now->copy()->addSeconds(15));
    }

    /**
     * @throws \Throwable
     */
    public function testVerify(): void
    {
        /** @var User $user */
        $user = factory(User::class)->create([
            'phone' => '79000000000',
            'phone_verified' => false,
            'phone_verify_token' => $token = 'token',
            'phone_verify_token_expire' => $now = Carbon::now(),
        ]);
        self::assertFalse($user->isPhoneVerified());
        $user->verifyPhone($token, $now->copy()->subSeconds(15));
        self::assertTrue($user->isPhoneVerified());
    }

    /**
     * @throws \Throwable
     */
    public function testVerifyIncorrectToken(): void
    {
        /** @var User $user */
        $user = factory(User::class)->create([
            'phone' => '79000000000',
            'phone_verified' => false,
            'phone_verify_token' => 'token',
            'phone_verify_token_expire' => $now = Carbon::now(),
        ]);
        $this->expectExceptionMessage('Incorrect verify token.');
        $user->verifyPhone('other_token', $now->copy()->subSeconds(15));
    }

    /**
     * @throws \Throwable
     */
    public function testVerifyExpiredToken(): void
    {
        /** @var User $user */
        $user = factory(User::class)->create([
            'phone' => '79000000000',
            'phone_verified' => false,
            'phone_verify_token' => $token = 'token',
            'phone_verify_token_expire' => $now = Carbon::now(),
        ]);
        $this->expectExceptionMessage('Token is expired.');
        $user->verifyPhone($token, $now->copy()->addSeconds(500));
    }
}