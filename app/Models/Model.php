<?php
/**
 * Created by ASUS.
 * Date: 8/1/2017
 * Time: 12:34 PM
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model as BaseModel;

/**
 * Class Model
 * @package App\Models
 * @method static|Model find(...$args)
 * @method static|Model select(...$args)
 * @method static|Model from(...$args)
 * @method static|Model join(...$args)
 * @method static|Model leftJoin(...$args)
 * @method static|Model where($field, $valueOrOperator = '=', $optionValue = '')
 * @method static|Model whereNull($value)
 * @method static|Model orWhere($field, $valueOrOperator = '=', $optionValue = '')
 * @method static|Model whereDate(...$args)
 * @method static|Model whereIn(...$args)
 * @method static|Model whereNotIn(...$args)
 * @method static|Model whereOnDate(...$args)
 * @method static|Model whereYear(...$args)
 * @method static|Model whereMonth(...$args)
 * @method static|Model orderBy(...$args)
 * @method static|Model groupBy(...$args)
 * @method static|Model having(...$args)
 * @method static|Model havingRaw(...$args)
 * @method static|Model limit(...$args)
 * @method Collection get(...$args)
 * @method static first(...$args)
 * @method int count(...$args)
 */
class Model extends BaseModel
{
    /**
     * Get a new query builder instance for the connection.
     * @return \Illuminate\Database\Query\Builder
     */
    protected function newBaseQueryBuilder()
    {
        $connection = $this->getConnection();

        return new AdvancedBuilder(
            $connection, $connection->getQueryGrammar(), $connection->getPostProcessor()
        );
    }
}