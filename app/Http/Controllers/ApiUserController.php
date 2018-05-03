<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Repositories\UsersRepository;

class ApiUserController extends Controller
{
    /**
     * list all news.
     * @param CreateUserRequest $request
     * @param UsersRepository $userRepository
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function index(CreateUserRequest $request, UsersRepository $userRepository)
    {
        // Get news list.
        $data = $userRepository->getAllUsers($request->all());

        return $this->response($data);
    }
}