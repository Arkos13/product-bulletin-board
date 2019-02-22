<?php

namespace App\Http\Controllers\Api\User;

use App\Entity\User\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cabinet\Profile\UpdateRequest;
use App\Http\Resources\User\ProfileResource;
use App\Http\Serializers\UserSerializer;
use App\Services\Profile\ProfileService;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    private $service;

    /**
     * ProfileController constructor.
     * @param ProfileService $profileService
     */
    public function __construct(ProfileService $profileService)
    {
        $this->service = $profileService;
    }

    /**
     * @SWG\Get(
     *     path="/user",
     *     tags={"Profile"},
     *     @SWG\Response(
     *         response=200,
     *         description="Success response",
     *         @SWG\Schema(ref="#/definitions/Profile"),
     *     ),
     *     security={{"Bearer": {}, "OAuth2": {}}}
     * )
     * @param Request $request
     * @return ProfileResource
     */
    public function show(Request $request)
    {
        return new ProfileResource($request->user());
    }

    /**
     *  @SWG\Put(
     *     path="/user",
     *     tags={"Profile"},
     *     @SWG\Parameter(name="body", in="body", required=true, @SWG\Schema(ref="#/definitions/ProfileEditRequest")),
     *     @SWG\Response(
     *         response=200,
     *         description="Success response",
     *     ),
     *     security={{"Bearer": {}, "OAuth2": {}}}
     * )
     * @param UpdateRequest $request
     * @return ProfileResource
     * @throws \Throwable
     */
    public function update(UpdateRequest $request)
    {
        $this->service->edit($request->user()->id, $request);
        /** @var User $user */
        $user = User::query()->findOrFail($request->user()->id);
        return new ProfileResource($user);
    }
}