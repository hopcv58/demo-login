<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use DB;

class Order extends Model
{
    use EloquentTrait;

    protected $table = 'orders';

    public $timestamps = true;


}
