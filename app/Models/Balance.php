<?php

namespace App\Models;

use Carbon\Carbon;
use DB;

class Balance extends Model
{
    use EloquentTrait;

    protected $table = 'balances';

    public $timestamps = true;

    /**
     * @param $currencyId
     * @param $amount
     * @param $user
     * @return mixed
     */
    public function deposit($currencyId, $amount, $user)
    {
        $balance = DB::table('balances')->where('user_id', $user->id)
            ->where('currency_id', $currencyId)
            ->first();
        if (!$balance) {
            $balanceId = DB::table('balances')->insertGetId([
                'user_id' => $user->id,
                'currency_id' => $currencyId,
                'amount' => $amount
            ]);

            return $balanceId;
        } else {
            DB::table('balances')->where('user_id', '=', $user->id)
                ->where('currency_id', '=', $currencyId)
                ->update([
                    'amount' => $balance->amount + $amount
                ]);

            return $balance->id;
        }
    }

    /**
     * @param $currencyId
     * @param $amount
     * @param $user
     * @return mixed
     */
    public function withdraw($currencyId, $amount, $userId)
    {
        $balance = DB::table('balances')->where('user_id', $userId)
            ->where('currency_id', $currencyId)
            ->first();
        DB::table('balances')->where('user_id', '=', $userId)
            ->where('currency_id', '=', $currencyId)
            ->update([
                'amount' => $balance->amount - $amount
            ]);

        return $balance->id;
    }

    public function info($balanceId, $userId)
    {
        return DB::table('balances')
            ->join('currencies', 'balances.currency_id', '=', 'currencies.id')
            ->where('balances.id', $balanceId)
            ->where('balances.user_id', $userId)
            ->first();
    }

    public function getBalanceByUser($user)
    {
        return DB::table('balances')
            ->join('currencies', 'balances.currency_id', '=', 'currencies.id')
            ->where('balances.user_id', $user->id)
            ->get();
    }

    public function getBalanceByUserAndCurrecy($userId, $currencyId)
    {
        $balance = DB::table('balances')
            ->where('user_id', $userId)
            ->where('currency_id', $currencyId)
            ->first();
        if (!$balance) {
            $balanceId = DB::table('balances')->insertGetId([
                'user_id' => $userId,
                'currency_id' => $currencyId,
                'amount' => 0,
                'frozen_amount' => 0,
                'address' => null
            ]);
            return $balance = DB::table('balances')
                ->where('id', $balanceId)
                ->first();
        } else {
            return $balance;
        }
    }

    public function getBalanceByDemands($demands)
    {
        $query = DB::table('balances');
        if ($shortName = array_pull($demands, 'short_name')) {
            $query = $query->join('currencies', 'currencies.id', '=', 'balances.currency_id')
                ->where('currencies.short_name', $shortName)
                ->select('balances.*');
        }
        foreach ($demands as $key => $value) {
            $query = $query->where($key, $value);
        }
        return $query->first();
    }

    public function getWithdrawByDemands($demands)
    {
        $query = DB::table('withdraw');
        foreach ($demands as $key => $value) {
            $query = $query->where($key, $value);
        }
        return $query->first();
    }

    public function createBalance($data)
    {
        return $query = DB::table('balances')->insertGetId($data);
    }

    public function createDeposit($data)
    {
        return $query = DB::table('deposit')->insertGetId($data);
    }

    public function updateBalance($id, $data)
    {
        return $query = DB::table('balances')->where('id', $id)->update($data);
    }

    public function updateWithdraw($id, $data)
    {
        return $query = DB::table('withdraw')->where('id', $id)->update($data);
    }

    public function getBalanceByUserAndCurrency($user, $currencyId)
    {
        return DB::table('balances')
            ->join('currencies', 'balances.currency_id', '=', 'currencies.id')
            ->where('balances.user_id', $user->id)
            ->where('balances.currency_id', $currencyId)
            ->first();
    }
}

