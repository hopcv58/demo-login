<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use DB;

class Order extends Model
{
    use EloquentTrait;

    protected $table = 'orders';

    public $timestamps = true;

    public function getAllOrderByUser($user)
    {
        return DB::table('trades')
            ->join('orders as buy_order', 'buy_order.id', '=', 'trades.buy_order_id')
            ->join('orders as sell_order', 'sell_order.id', '=', 'trades.sell_order_id')
            ->join('users as buyer', 'buy_order.user_id', '=', 'buyer.id')
            ->join('users as seller', 'sell_order.user_id', '=', 'seller.id')
            ->where('trades.created_at', '>', Carbon::today()->subDays(30)->format('Y-m-d'))
            ->where(function ($query) use ($user) {
                $query->where('buyer.id', '=', $user->id)
                    ->orWhere('seller.id', '=', $user->id);
            })
            ->select('trades.*')
            ->get();
    }

    public function getPendingOrderByUser($user)
    {
        return DB::table('trades')
            ->join('orders as buy_order', 'buy_order.id', '=', 'trades.buy_order_id')
            ->join('orders as sell_order', 'sell_order.id', '=', 'trades.sell_order_id')
            ->join('users as buyer', 'buy_order.user_id', '=', 'buyer.id')
            ->join('users as seller', 'sell_order.user_id', '=', 'seller.id')
            ->where(function ($query) use ($user) {
                $query->where('buyer.id', '=', $user->id)
                    ->orWhere('seller.id', '=', $user->id);
            })
            ->where(function ($query) {
                $query->where('buy_order.order_status', '=', 4)//4: pending
                ->orWhere('sell_order.order_status', '=', 4); //4: pending
            })
            ->select('trades.*')
            ->get();
    }

    public function createOrder($demands)
    {
        return DB::table('orders')
            ->insertGetId($demands);
    }

    public function createTrade($demands)
    {
        return DB::table('trades')
            ->insertGetId($demands);
    }

    public function getOrderByDemand($demands)
    {
        $query = DB::table('orders');
        foreach ($demands as $key => $value) {
            $query = $query->where($key, $value);
        }
        return $query->first();
    }

    public function getAllOrdersByDemand($demands)
    {
        $query = DB::table('orders');
        foreach ($demands as $key => $value) {
            $query = $query->where($key, $value);
        }
        return $query->get();
    }

    public function updateOrder($orderId, $orderData)
    {
        return DB::table('orders')
            ->where('id', $orderId)
            ->update($orderData);
    }
}
