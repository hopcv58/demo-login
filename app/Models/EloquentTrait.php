<?php
/**
 * Created by Co-well.
 * Date: 7/18/2017
 * Time: 11:21 AM
 */

namespace App\Models;


trait EloquentTrait
{
    /**
     * Get table name of model.
     * @return string
     */
    public static function getTableName()
    {
        return ((new self)->getTable());
    }
}
