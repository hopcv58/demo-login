<?php

namespace App\Http\Controllers;

use App\Http\Requests\TradeAllRequest;
use App\Http\Requests\TradePendingRequest;
use App\Repositories\BalanceRepository;

class ApiTradeController extends Controller
{

    public function all(TradeAllRequest $request, BalanceRepository $balanceRepository) {
//        pushToFrontEnd(['message' => 'success']);
        $data = $balanceRepository->allTrade($request->user());
        return $this->response($data);
    }

    public function pending(TradePendingRequest $request, BalanceRepository $balanceRepository) {
//        pushToFrontEnd(['message' => 'success']);
        $data = $balanceRepository->getPendingOrder($request->user());
        return $this->response($data);
    }
}