<?php
/**
 * Created by ASUS.
 * Date: 7/18/2017
 * Time: 12:25 PM
 */

namespace App\Repositories;

use DB;

class Repository
{
    /**
     * Start transaction.
     */
    public static function transaction ()
    {
        DB::beginTransaction();
    }

    /**
     * Roll back.
     */
    public static function rollback ()
    {
        DB::rollBack();
    }

    /**
     * Roll back.
     */
    public static function commit ()
    {
        DB::commit();
    }
}