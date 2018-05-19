<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use DB;

class Deposit extends Model
{
    use EloquentTrait;

    protected $table = 'deposit';

    public $timestamps = true;

    public function deposit($currencyId, $amount, $user)
    {
        return DB::table('deposit')->insertGetId([
            'user_id' => $user->id,
            'currency_id' => $currencyId,
            'amount' => $amount
        ]);
    }
}
