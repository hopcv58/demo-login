<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use DB;

class Currency extends Model
{
    use EloquentTrait;

    protected $table = 'currencies';

    public $timestamps = true;


}
