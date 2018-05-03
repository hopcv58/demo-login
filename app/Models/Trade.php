<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use DB;

class Trade extends Model
{
    use EloquentTrait;

    protected $table = 'trades';

    public $timestamps = true;


}
