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
        $this->withdraw = new Withdraw();
    }

    public function deposit($currencyId, $amount, $user)
    {
        $depositId = $this->deposit->deposit($currencyId, $amount, $user);
        $balanceId = $this->balances->deposit($currencyId, $amount, $user);
        $balanceAfterDeposit = $this->balances->info($balanceId);
        return $balanceAfterDeposit;
    }

    public function withdraw($currencyId, $amount, $user)
    {
        $depositId = $this->withdraw->withdraw($currencyId, $amount, $user);
        $balanceId = $this->balances->withdraw($currencyId, $amount, $user);
        $balanceAfterDeposit = $this->balances->info($balanceId, $user);
        return $balanceAfterDeposit;
    }

    public function all($user)
    {
        return $this->balances->getBalanceByUser($user);
    }
}