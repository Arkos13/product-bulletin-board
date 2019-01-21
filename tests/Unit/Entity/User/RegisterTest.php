<?php

namespace Tests\Unit\Entity\User;

use App\Entity\User;
use App\Repositories\UserRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use DatabaseTransactions;

    public function testRegister(): void
    {
        /** @var User $user */
        $user = UserRepository::register(
            $name = 'name',
            $email = 'email',
            $psw = 'password'
        );
        self::assertNotEmpty($user);
        self::assertEquals($name, $user->name);
        self::assertEquals($email, $user->email);
        self::assertNotEmpty($user->password);
        self::assertNotEquals($psw, $user->password);
        self::assertTrue($user->isWait());
        self::assertFalse($user->isActive());
    }

    public function testVerify(): void
    {
        /** @var User $user */
        $user = UserRepository::register('name', 'email', 'password');
        $user->verify();
        self::assertFalse($user->isWait());
        self::assertTrue($user->isActive());
    }

    public function testAlreadyVerified(): void
    {
        /** @var User $user */
        $user = UserRepository::register('name', 'email', 'password');
        $user->verify();
        $this->expectExceptionMessage('User is already verified.');
        $user->verify();
    }
}