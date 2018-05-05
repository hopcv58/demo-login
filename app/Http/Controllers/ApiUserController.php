<?php

namespace App\Http\Controllers;

use App\Http\Requests\AllUserRequest;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\GetUserInfoRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Repositories\UsersRepository;

class ApiUserController extends Controller
{
    /**
     * create user
     * @param CreateUserRequest $request
     * @param UsersRepository $userRepository
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function create(CreateUserRequest $request, UsersRepository $userRepository)
    {
        // Get news list.
        $id = $userRepository->createNewUser($request->all());

        return $this->response([
            'user_id' => $id
        ]);
    }

    /**
     * get user's info
     * @param GetUserInfoRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function info(GetUserInfoRequest $request)
    {
        return $this->response($request->user());
    }

    /**
     * update user
     * @param UpdateUserRequest $request
     * @param UsersRepository $userRepository
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function update(UpdateUserRequest $request, UsersRepository $userRepository)
    {
        // Get news list.
        $data = $userRepository->updateUserById($request->user()->id, $request->all());

        return $this->response([
            'success' => $data
        ]);
    }

    /**
     * get all user
     * @param AllUserRequest $request
     * @param UsersRepository $userRepository
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function all(AllUserRequest $request, UsersRepository $userRepository)
    {
        // Get news list.
        $data = $userRepository->allUsers();

        return $this->response($data);
    }
}