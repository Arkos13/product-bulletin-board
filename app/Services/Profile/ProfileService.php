<?php

namespace App\Services\Profile;

use App\Entity\User\User;
use App\Http\Requests\Cabinet\Profile\UpdateRequest;

class ProfileService
{
    /**
     * @param $id
     * @param UpdateRequest $request
     * @throws \Throwable
     */
    public function edit($id, UpdateRequest $request)
    {
        /** @var User $user */
        $user = User::query()->findOrFail($id);
        $oldPhone = $user->phone;
        $user->update($request->only('name', 'last_name', 'phone'));
        if ($user->phone !== $oldPhone) {
            $user->unverifyPhone();
        }
    }
}