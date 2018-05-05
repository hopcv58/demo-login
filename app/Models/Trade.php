<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use DB;

class Trade extends Model
{
    use EloquentTrait;

    protected $table = 'trades';

    public $timestamps = true;

    public function getAllTradeByUser(User $user)
    {
        DB::table('trade')
            ->join('orders as buy_order', 'buy_order.id', '=', 'trades.buy_order_id')
            ->join('orders as sell_order', 'sell_order.id', '=', 'trades.sell_order_id')
            ->join('users as buyer', 'buy_order.user_id', '=', 'buyer.id')
            ->join('users as seller', 'sell_order.user_id', '=', 'seller.id')
            ->where('buyer.id', '=', $user->id)
            ->orWhere('seller.id', '=', $user->id);

    }
}
