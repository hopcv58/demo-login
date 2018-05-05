<?php

namespace App\Models;

use Carbon\Carbon;
use DB;

class Withdraw extends Model
{
    use EloquentTrait;

    protected $table = 'withdraw';

    public $timestamps = true;


    public function withdraw($currencyId, $amount, $user)
    {
        return DB::table('withdraw')->insertGetId([
            'user_id' => $user->id,
            'currency_id' => $currencyId,
            'amount' => $amount,
            'created_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
    }
}
