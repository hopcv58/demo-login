<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use DB;

class Currency extends Model
{
    use EloquentTrait;

    protected $table = 'currencies';

    public $timestamps = true;

    public function getCurrencyByDemand($demand)
    {
        $query = DB::table('currencies');
        foreach ($demand as $key => $value) {
            $query = $query->where($key, $value);
        }
        return $query->first();
    }
}
