<?php
/**
 * Created by TuyenNV.
 * Date: 8/14/2016
 * Time: 11:05 AM
 */

namespace App\Components\Utilities\Filter;


class Filter
{
    /**
     * All of the available operators.
     */
    const OPERATORS = [
        '=', '<', '>', '<=', '>=', '<>', '!=', '<=>',
        'like', 'like binary', 'not like', 'between', 'ilike',
        '&', '|', '^', '<<', '>>',
        'rlike', 'regexp', 'not regexp',
        '~', '~*', '!~', '!~*', 'similar to',
        'not similar to', 'not ilike', '~~*', '!~~*',
    ];

    /**
     * @var string|int|array
     */
    protected $value;

    /**
     * @var string
     */
    protected $operator;

    /**
     * Condition constructor.
     * @param null|string|int|array $value
     * @param null|string $operator
     */
    public function __construct($value = null, $operator = null)
    {
        if ($value !== null) {
            $this->value = $value;
            $this->operator = $operator ?: '=';

        }
    }

    /**
     * @return int|string|array
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param int|string|array $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getOperator()
    {
        return $this->operator;
    }

    /**
     * @param string $operator
     * @throws \Exception
     */
    public function setOperator($operator = '=')
    {
        if (! in_array($operator, static::OPERATORS)) {
            throw new \Exception('Operator invalid.');
        }

        $this->operator = $operator;
    }
}