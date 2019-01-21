<?php

namespace App\Services\Auth;

use App\Entity\User;
use App\Http\Requests\Auth\RegisterRequest;
use App\Mail\VerifyMail;
use App\Repositories\UserRepository;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Mail\Mailer;

class RegisterService
{
    /**
     * @var Mailer
    */
    private $mailer;

    /**
     * @var Dispatcher
    */
    private $dispatcher;

    /**
     * RegisterService constructor.
     * @param Mailer $mailer
     * @param Dispatcher $dispatcher
     */
    public function  __construct(Mailer $mailer, Dispatcher $dispatcher)
    {
        $this->mailer = $mailer;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param RegisterRequest $request
     */
    public function register(RegisterRequest $request): void
    {
        /** @var User $user*/
        $user = UserRepository::register(
            $request['name'],
            $request['email'],
            $request['password']
        );
        $this->mailer->to($user->email)->send(new VerifyMail($user));
        $this->dispatcher->dispatch(new Registered($user));
    }

    /**
     * @param $id
     */
    public function verify($id): void
    {
        /** @var User $user */
        $user = User::query()->findOrFail($id);
        $user->verify();
    }
}