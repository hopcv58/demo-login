<?php

namespace App\Http\Controllers;

use App\Http\Requests\BalanceAllRequest;
use App\Http\Requests\DepositRequest;
use App\Http\Requests\WithdrawRequest;
use App\Repositories\BalanceRepository;

class ApiBalanceController extends Controller
{
    /**
     * deposit
     * @param DepositRequest $request
     * @param BalanceRepository $userRepository
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function deposit(DepositRequest $request, BalanceRepository $userRepository)
    {
        // Get news list.
        $balanceAfterDeposit = $userRepository->deposit($request->currency_id, $request->amount, $request->user());

        return $this->response([
            'currency' => $balanceAfterDeposit->short_name,
            'deposited_amount' => $request->amount,
            'frozen_amount' => $balanceAfterDeposit->frozen_amount,
            'total' => $balanceAfterDeposit->amount,
        ]);
    }

    /**
     * withdraw
     * @param WithdrawRequest $request
     * @param BalanceRepository $userRepository
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function withdraw(WithdrawRequest $request, BalanceRepository $userRepository)
    {
        // Get news list.
        $data = $userRepository->withdraw($request->currency_id, $request->amount, $request->user());

        if ($data['error'] == 0) {
            $balanceAfterDeposit = $data['data'];
            return $this->response([
                'currency' => $balanceAfterDeposit->short_name,
                'deposited_amount' => $request->amount,
                'frozen_amount' => $balanceAfterDeposit->frozen_amount,
                'total' => $balanceAfterDeposit->amount,
            ]);
        } else {
            return $this->response(null, $data['message']);
        }

    }

    public function all(BalanceAllRequest $request, BalanceRepository $balanceRepository) {
        $data = $balanceRepository->all($request->user());
        return $this->response($data);
    }
}