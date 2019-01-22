<?php

class UsersTableSeeder extends \Illuminate\Database\Seeder
{
    public function run(): void
    {
        factory(\App\Entity\User::class, 10)->create();
    }
}