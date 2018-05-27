<?php

namespace App\Http\Controllers;

use App\Components\Utilities\Responder;
use App\Http\Requests\CreateOrderRequest;
use App\Http\Requests\OrderListRequest;
use App\Http\Requests\TradeAllRequest;
use App\Http\Requests\TradePendingRequest;
use App\Repositories\BalanceRepository;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class ApiTradeController extends Controller
{

    public function all(TradeAllRequest $request, BalanceRepository $balanceRepository)
    {
//        $data = pushToFrontEnd(['message' => 'success']);
        $data = $balanceRepository->allTrade($request->user());
        return $this->response($data);
    }

    public function pending(TradePendingRequest $request, BalanceRepository $balanceRepository)
    {
//        pushToFrontEnd(['message' => 'success']);
        $data = $balanceRepository->getPendingOrder($request->user());
        return $this->response($data);
    }

    public function createOrder(CreateOrderRequest $request, BalanceRepository $balanceRepository)
    {
        if ($request->order_side == 1) {
            // buy_side
            // freeze USD balance
            $usdCurrency = $balanceRepository->getCurrencyByDemand([
                'short_name' => 'USD'
            ]);
            $freezeResult = $balanceRepository->freezeAmount($request->user()->id, $usdCurrency->id,
                $request->order_size * $request->order_price);
            if (!$freezeResult) {
                return Responder::invalid([
                    'currency_id' => 'You dont have enough balance left!'
                ], 'invalid');
            }
        } else {
            // sell side
            // freeze bitcoin balance
            $coinCurrency = $balanceRepository->getCurrencyByDemand([
                'id' => $request->currency_id
            ]);
            $freezeResult = $balanceRepository->freezeAmount($request->user()->id, $coinCurrency->id,
                $request->order_size);
            if (!$freezeResult) {
                return Responder::invalid([
                    'currency_id' => 'You dont have enough balance left!'
                ], 'invalid');
            }
        }
        // create Order
        $orderData = [
            'user_id' => $request->user()->id,
            'order_size' => $request->order_size,
            'currency_id' => $request->currency_id,
            'order_price' => $request->order_price,
            'is_buy_side' => $request->order_side,  // buy side:1
            'order_type' => $request->order_type,
            'order_status' => 0, //0:init
        ];
        $orderedCurrency = $balanceRepository->getCurrencyByDemand([
            'id' => $request->currency_id
        ]);
        $orderId = $balanceRepository->createOrder($orderData);
        //push to rabbit
        $exchange = $orderedCurrency->short_name . '-USD';
        $rabbitData = [
            'id' => $orderId,
            'side' => $request->order_side,
            'type' => $request->order_type,
            'price' => $request->order_price,
            'size' => $request->order_size,
        ];
        pushToRabbit($exchange, 'enter', json_encode($rabbitData, JSON_FORCE_OBJECT));

        $newOrder = $balanceRepository->getOrderByDemand([
            'id' => $orderId
        ]);
        pushToFrontEnd('order_channel', 'all_orders', $newOrder);
        return $this->response(null, 'Create order successfully!');
    }

    public function allOrder(OrderListRequest $request, BalanceRepository $balanceRepository)
    {
        $data = $balanceRepository->getAllOrdersByDemand([]);
        return $this->response($data);
    }

    public function listOrder(OrderListRequest $request, BalanceRepository $balanceRepository)
    {
        $data = $balanceRepository->getAllOrdersByDemand(['user_id' => $request->user()->id]);
        return $this->response($data);
    }
}