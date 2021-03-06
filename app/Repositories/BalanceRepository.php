<?php
/**
 * Created by ASUS.
 * Date: 7/21/2017
 * Time: 2:53 PM
 */

namespace App\Repositories;

use App\Models\Balance;
use App\Models\Currency;
use App\Models\Deposit;
use App\Models\Order;
use App\Models\Trade;
use App\Models\Withdraw;
use BitWasp\BitcoinLib\BIP32;
use Carbon\Carbon;

class BalanceRepository extends Repository
{
    /**
     * @var Balance $balances
     */
    private $balances;

    /**
     * @var Currency $currencies
     */
    private $currencies;

    /**
     * @var Deposit $deposit
     */
    private $deposit;

    /**
     * @var Trade $trade
     */
    private $trade;

    /**
     * @var Order $order
     */
    private $order;

    /**
     * @var Withdraw $withdraw
     */
    private $withdraw;

    /**
     * UsersRepository constructor.
     */
    public function __construct()
    {
        $this->balances = new Balance();
        $this->currencies = new Currency();
        $this->deposit = new Deposit();
        $this->order = new Order();
        $this->trade = new Trade();
        $this->withdraw = new Withdraw();
    }

    public function deposit($amount, $user)
    {
        $currency = $this->getCurrencyByDemand([
            'short_name' => 'USD'
        ]);
        $depositId = $this->deposit->deposit($currency->id, $amount, $user);
        $balanceId = $this->balances->deposit($currency->id, $amount, $user);
        $balanceAfterDeposit = $this->balances->info($balanceId, $user->id);
        return $balanceAfterDeposit;
    }

    public function withdraw($currencyId, $amount, $user)
    {
        $balance = $this->balances->getBalanceByUserAndCurrency($user, $currencyId);
        if (!$balance) {
            return [
                'error' => 1,
                'message' => 'You dont have any of this kind!',
                'data' => null
            ];
        }
        if ($balance->amount < $amount) {
            $balanceAfterWithdraw = $this->balances->info($balance->id, $user->id);
            return [
                'error' => 1,
                'message' => 'You dont have enough balance!',
                'data' => null
            ];
        } else {
            $currency = $this->getCurrencyByDemand([
                'short_name' => 'USD'
            ]);
            if($currencyId != $currency->id) {
                $withdrawId = $this->withdraw->withdraw($currencyId, $amount, $user);
                $address = $this->getAddress($user->id, $currencyId);
                pushToRabbit('bitcoin', 'withdraw', json_encode([
                    'id' => $withdrawId,
                    'address' => $address,
                    'amount' => $amount
                ]));
            }
            return [
                'error' => 0,
                'message' => 'Withdraw successfully',
                'data' => $withdrawId
            ];
        }

    }

    public function all($user)
    {
        return $this->balances->getBalanceByUser($user);
    }

    public function allTrade($user)
    {
        return $this->order->getAllOrderByUser($user);
    }

    public function getPendingOrder($user)
    {
        return $this->order->getPendingOrderByUser($user);
    }

    public function getOrderByDemand($demand)
    {
        return $this->order->getOrderByDemand($demand);
    }

    public function getAllOrdersByDemand($demand)
    {
        return $this->order->getAllOrdersByDemand($demand);
    }

    public function createOrder($data)
    {
        return $this->order->createOrder($data);
    }

    public function createTrade($data)
    {
        return $this->order->createTrade($data);
    }

    public function getCurrencyByDemand($demand)
    {
        return $this->currencies->getCurrencyByDemand($demand);
    }

    public function getBalanceByUserAndCurrency($userId, $currencyId)
    {
        return $this->balances->getBalanceByUserAndCurrecy($userId, $currencyId);
    }

    public function getBalanceByDemands($demands)
    {
        return $this->balances->getBalanceByDemands($demands);
    }

    public function getWithdrawByDemands($demands)
    {
        return $this->balances->getWithdrawByDemands($demands);
    }

    public function createBalance($demands)
    {
        return $this->balances->createBalance($demands);
    }

    public function createDeposit($demands)
    {
        return $this->balances->createDeposit($demands);
    }

    public function updateBalance($id, $demands)
    {
        return $this->balances->updateBalance($id, $demands);
    }

    public function updateWithdraw($id, $demands)
    {
        return $this->balances->updateWithdraw($id, $demands);
    }

    public function freezeAmount($userId, $currencyId, $freezeAmount)
    {
        $balance = $this->balances->getBalanceByUserAndCurrecy($userId, $currencyId);
        if ($balance->amount < $freezeAmount) {
            return false;
        }

        return $this->balances->updateBalance($balance->id, [
            'amount' => $balance->amount - $freezeAmount,
            'frozen_amount' => $balance->frozen_amount + $freezeAmount
        ]);
    }

    public function updateOrder($orderId, $orderData) {
        return $this->order->updateOrder($orderId, $orderData);
    }

    public function getAddress($userId, $currencyId) {
        $balance = $this->getBalanceByDemands([
            'user_id' => $userId,
            'currency_id' => $currencyId
        ]);
        if (!$balance) {
            $pub=config('rabbit.bitcoin_pubkey');
            $nextpub = BIP32::build_key($pub, $userId);
            $address = BIP32::key_to_address($nextpub[0]);

            $this->createBalance([
                'user_id' => $userId,
                'currency_id' => $currencyId,
                'amount' => 0,
                'frozen_amount' => 0,
                'address' => $address
            ]);
            return $address;
        } else {
            return $balance->address;
        }
    }
}