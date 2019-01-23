<?php

namespace Tests\Unit\Entity\User;

use App\Entity\User;
use App\Repositories\UserRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CreateTest extends TestCase
{
    use DatabaseTransactions;

    public function testNew(): void
    {
        /** @var User $user */
        $user = UserRepository::new($name = 'name', $email = 'email');
        self::assertNotEmpty($user);
        self::assertEquals($name, $user->name);
        self::assertEquals($email, $user->email);
        self::assertNotEmpty($user->password);
        self::assertTrue($user->isActive());
        self::assertFalse($user->isAdmin());
    }
}