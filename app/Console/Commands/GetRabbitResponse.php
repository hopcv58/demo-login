<?php

namespace App\Console\Commands;

use App\Models\Balance;
use App\Repositories\BalanceRepository;
use Illuminate\Console\Command;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class GetRabbitResponse extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rabbit:listen';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Listen from rabbit mq service';

    private $balanceRepository;

    private $balances;


    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->balanceRepository = new BalanceRepository();
        $this->balances = new Balance();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $connection = new AMQPStreamConnection(config('rabbit.host'), config('rabbit.port'),
            config('rabbit.user'), config('rabbit.pass'));
        $channel = $connection->channel();
        $exchange = config('rabbit.exchange');
        $channel->exchange_declare($exchange, 'direct', false, false, false);

        list($queueName, ,) = $channel->queue_declare("", false, false, true, false);
        $channel->queue_bind($queueName, $exchange, 'add');
        $channel->queue_bind($queueName, $exchange, 'cancel');
        $channel->queue_bind($queueName, $exchange, 'match');
        $channel->queue_bind($queueName, $exchange, 'deposit.coin.BTC');
        $channel->queue_bind($queueName, $exchange, 'withdraw.coin.BTC');
        echo ' [*] Listening from RabbitMq service. To exit press CTRL+C', "\n";

        //receive
        $callback = function ($msg) {
            $routeKey = $msg->delivery_info['routing_key'];
            $response = json_decode($msg->body);
            switch ($routeKey) {
                case "add":
                    $order = $this->balanceRepository->updateOrder($response->id, [
                        'order_status' => array_search($response->status, config('rabbit.order_status')),
                        'order_size' => $response->size
                    ]);
                    break;
                case "match":
                    $restingOrder = $this->balanceRepository->getOrderByDemand([
                        'id' => $response->restingOrderId,
                    ]);

                    $incomingOrder = $this->balanceRepository->getOrderByDemand([
                        'id' => $response->incomingOrderId
                    ]);
                    $tradeId = $this->balanceRepository->createTrade([
                        'buy_order_id' => $restingOrder->is_buy_side ? $restingOrder->id : $incomingOrder->id,
                        'sell_order_id' => $restingOrder->is_buy_side ? $incomingOrder->id : $restingOrder->id,
                        'currency_id' => $incomingOrder->currency_id,
                        'trade_price' => $response->price,
                        'trade_size' => $response->executedQuantity
                    ]);

                    //create Address for resting
                    $this->balanceRepository->getAddress($restingOrder->user_id, $restingOrder->currency_id);
                    //update balances
                    $restingCoin = $this->balanceRepository->getBalanceByDemands([
                        'user_id' => $restingOrder->user_id,
                        'currency_id' => $restingOrder->currency_id
                    ]);
                    $restingUsd = $this->balanceRepository->getBalanceByDemands([
                        'user_id' => $restingOrder->user_id,
                        'short_name' => 'USD'
                    ]);
                    //create Address for incoming
                    $this->balanceRepository->getAddress($incomingOrder->user_id, $incomingOrder->currency_id);
                    $incomingCoin = $this->balanceRepository->getBalanceByDemands([
                        'user_id' => $incomingOrder->user_id,
                        'currency_id' => $incomingOrder->currency_id
                    ]);
                    $incomingUsd = $this->balanceRepository->getBalanceByDemands([
                        'user_id' => $incomingOrder->user_id,
                        'short_name' => 'USD'
                    ]);
                    if ($restingOrder->is_buy_side == 1) {
                        $this->balanceRepository->updateBalance($restingCoin->id, [
                            'amount' => $restingCoin->amount + $restingOrder->order_size - $response->remainingQuantity,
                        ]);
                        $this->balanceRepository->updateBalance($restingUsd->id, [
                            'frozen_amount' => $restingCoin->frozen_amount -
                                ($restingOrder->order_size - $response->remainingQuantity) * $response->price,
                        ]);
                        $this->balanceRepository->updateBalance($incomingCoin->id, [
                            'frozen_amount' => $incomingCoin->amount - ($restingOrder->order_size - $response->remainingQuantity),
                        ]);
                        $this->balanceRepository->updateBalance($incomingUsd->id, [
                            'amount' => $incomingUsd->amount + ($restingOrder->order_size - $response->remainingQuantity) * $response->price,
                        ]);
                    } else {
                        $this->balanceRepository->updateBalance($incomingCoin->id, [
                            'amount' => $incomingCoin->amount + $restingOrder->order_size - $response->remainingQuantity,
                        ]);
                        $this->balanceRepository->updateBalance($incomingUsd->id, [
                            'frozen_amount' => $incomingUsd->frozen_amount - ($restingOrder->order_size - $response->remainingQuantity) * $response->price,
                        ]);
                        $this->balanceRepository->updateBalance($restingCoin->id, [
                            'frozen_amount' => $restingCoin->amount - ($restingOrder->order_size - $response->remainingQuantity),
                        ]);
                        $this->balanceRepository->updateBalance($restingUsd->id, [
                            'amount' => $restingCoin->amount + ($restingOrder->order_size - $response->remainingQuantity) * $response->price,
                        ]);
                    }

                    // update Order
                    $this->balanceRepository->updateOrder($restingOrder->id, [
                        'order_status' => array_search($response->status, config('rabbit.order_status')),
                        'order_size' => $response->remainingQuantity
                    ]);
                    break;
                case "cancel":
                    $this->balanceRepository->updateOrder($response->id, [
                        'order_status' => array_search($response->status, config('rabbit.order_status')),
                    ]);
                    $order = $this->balanceRepository->getOrderByDemand([
                        'id' => $response->id
                    ]);
                    if ($order->is_buy_side == 1) {
                        $balance = $this->balanceRepository->getBalanceByDemands([
                            'user_id' => $order->user_id,
                            'short_name' => 'USD'
                        ]);
                        $this->balanceRepository->updateBalance($balance->id, [
                            'amount' => $balance->amount + $order->order_size * $order->order_price,
                            'frozen_amount' => $balance->frozen_amount - $order->order_size * $order->order_price
                        ]);
                    } else {
                        $balance = $this->balanceRepository->getBalanceByDemands([
                            'user_id' => $order->user_id,
                            'currency_id' => $order->currency_id
                        ]);
                        $this->balanceRepository->updateBalance($balance->id, [
                            'amount' => $balance->amount + $order->order_size,
                            'frozen_amount' => $balance->frozen_amount - $order->order_size
                        ]);
                    }
                    break;
                case "deposit.coin.BTC":
                    //$response = [address:'aaaa', amount: 0.01, status: pending/success]
                    $balance = $this->balanceRepository->getBalanceByDemands([
                        'address' => $response->address
                    ]);
                    $this->balanceRepository->createDeposit([
                        'user_id' => $balance->user_id,
                        'currency_id' => $balance->currency_id,
                        'amount' => $response->amount
                    ]);
                    if ($response->status = 'success') {
                        $this->balanceRepository->updateBalance($balance->id, [
                            'amount' => $balance->amount + $response->amount
                        ]);
                    }
                    break;
                case "withdraw.coin.BTC":
                    //$response = [id:'1', status: 'success']
                    $withdraw = $this->balanceRepository->getWithdrawByDemands([
                        'id' => $response->id,
                        'is_success' => 0
                    ]);
                    $this->balanceRepository->updateWithdraw($response->id, [
                        'is_success' => 1
                    ]);
                    $balanceId = $this->balances->withdraw($withdraw->currency_id, $withdraw->amount, $withdraw->user_id);
                    $balanceAfterWithdraw = $this->balances->info($balanceId, $withdraw->user_id);

                    break;
            }
        };
        $channel->basic_consume($queueName, '', false, true, false, false, $callback);

        while (count($channel->callbacks)) {
            $channel->wait();
        }
    }
}
