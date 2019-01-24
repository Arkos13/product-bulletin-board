<?php

namespace App\Http\Controllers\Admin;

use App\Entity\User;
use App\Http\Requests\Admin\Users\{
    CreateRequest, UpdateRequest
};
use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use App\Services\Auth\RegisterService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * @var RegisterService
    */
    private $registerService;

    /**
     * UserController constructor.
     * @param RegisterService $registerService
     */
    public function __construct(RegisterService $registerService)
    {
        $this->registerService = $registerService;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $users = UserRepository::getUsers($request->all());

        $statuses = [
            User::STATUS_WAIT => 'Waiting',
            User::STATUS_ACTIVE => 'Active',
        ];
        $roles = [
            User::ROLE_USER => 'User',
            User::ROLE_ADMIN => 'Admin',
        ];
        return view('admin.users.index', compact('users', 'statuses', 'roles'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * @param CreateRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(CreateRequest $request)
    {
        $user = UserRepository::new($request['name'], $request['email']);
        return redirect()->route('admin.users.show', $user);
    }

    /**
     * @param User $user
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    /**
     * @param User $user
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(User $user)
    {
        $roles = [
            User::ROLE_USER => 'User',
            User::ROLE_ADMIN => 'Admin',
        ];
        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * @param UpdateRequest $request
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateRequest $request, User $user)
    {
        $user->update($request->only(['name', 'email']));
        if ($request['role'] !== $user->role) {
            $user->changeRole($request['role']);
        }
        return redirect()->route('admin.users.show', $user);
    }

    /**
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users.index');
    }

    /**
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function verify(User $user)
    {
        $this->registerService->verify($user->id);
        return redirect()->route('admin.users.show', $user);
    }
}
