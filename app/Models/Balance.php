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
                'amount' => $amount,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s')
            ]);

            return $balanceId;
        } else {
            DB::table('balances')->where('user_id', '=', $user->id)
                ->where('currency_id', '=', $currencyId)
                ->update([
                    'amount' => $balance->amount + $amount,
                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
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
    public function withdraw($currencyId, $amount, $user)
    {
        $balance = DB::table('balances')->where('user_id', $user->id)
            ->where('currency_id', $currencyId)
            ->first();
        DB::table('balances')->where('user_id', '=', $user->id)
            ->where('currency_id', '=', $currencyId)
            ->update([
                'amount' => $balance->amount - $amount,
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ]);

        return $balance->id;
    }

    public function info($balanceId, $user) {
        return DB::table('balances')
            ->join('currencies', 'balances.currency_id', '=', 'currencies.id')
            ->where('balances.id', $balanceId)
            ->where('balances.user_id', $user->id)
            ->first();
    }

    public function getBalanceByUser($user) {
        return DB::table('balances')
            ->join('currencies', 'balances.currency_id', '=', 'currencies.id')
            ->where('balances.user_id', $user->id)
            ->get();
    }

    public function getBalanceByUserAndCurrency($user, $currencyId) {
        return DB::table('balances')
            ->join('currencies', 'balances.currency_id', '=', 'currencies.id')
            ->where('balances.user_id', $user->id)
            ->where('balances.currency_id', $currencyId)
            ->first();
    }
}

