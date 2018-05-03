<?php
namespace App\Components\Utilities\Filter;

use IteratorAggregate ;

class Condition implements IteratorAggregate
{
    /**
     * Store all filters.
     * @var array.
     */
    private $filters = [];

    /**
     * Condition constructor.
     * @param array $conditions list condition.
     * @throws \Exception
     */
    public function __construct(array $conditions = [])
    {
        foreach ($conditions as $field => $condition) {
            // Condition: $filter.
            if (! $condition instanceof Filter) {
                throw new \Exception('Construct arg type: Array Filter instance.');
            }

            $this->filters[$field] = $condition;
        }
    }

    /**
     * Create new Condition instance by array values.
     * @param array $values
     * @return static
     */
    public static function instanceByValues (array $values)
    {
        $filters = [];

        foreach ($values as $field => $value) {
            $filters[$field] = new Filter($value);
        }

        return new static($filters);
    }

    /**
     * Create new Condition instance by array values.
     * @param array $values
     * @return static
     */
    public static function instanceByHash (array $values)
    {
        $filters = [];

        foreach ($values as $field => $value) {
            $operators = isset($value['operator']) ? $value['operator'] : '=';

            $filters[$field] = new Filter($value['value'], $operators);
        }

        return new static($filters);
    }

    /**
     * Iterator of Filters
     * @return array
     */
    public function getIterator() {
        return $this->filters;
    }

    /**
     * get value of filter by field name.
     * @param $field
     * @return mixed
     * @throws \Exception
     */
    public function has($field)
    {
        if (isset($this->filters[$field])) {
            return true;
        }

        return false;
    }

    /**
     * Get  filter by field name.
     * @param $field
     * @return Filter
     * @throws \Exception
     */
    public function get($field)
    {
        if (! isset($this->filters[$field])) {
            throw new \Exception("Not exists filter {$field}");
        }

        return $this->filters[$field];
    }

    /**
     * Get value of filter by field name.
     * @param $field
     * @return int|string
     */
    public function getValue($field)
    {
        return $this->get($field)->getValue();
    }

    /**
     * Get value of filter by field name.
     * @param $field
     * @return int|string
     */
    public function getOperator($field)
    {
        return $this->get($field)->getOperator();
    }

    /**
     * @param string $field
     * @param int|string $value
     * @param string $operator
     */
    public function set($field, $value, $operator = '=')
    {
        if (! $value instanceof Filter) {
            $filter = new Filter($value, $operator);

        } else {
            $filter = $value;
        }

        $this->filters[$field] = $filter;
    }

    /**
     * Set value of filter by field name.
     * @param string $field
     * @param int|string $value
     */
    public function setValue($field, $value)
    {
        return $this->set($field, $value);
    }

    /**
     * Set operator of filter by field name.
     * @param string $field
     * @param string $operator
     * @throws \Exception
     */
    public function setOperator($field, $operator)
    {
        if (! isset($this->filters[$field])) {
            throw new \Exception("Not exists filter {$field}");
        }

        $this->filters[$field]->setOperator($operator);
    }

    /**
     * @param $field
     * @return mixed
     */
    public function __get($field)
    {
        return $this->get($field);
    }

    /**
     * @param $field
     * @param array $args
     */
    public function __set($field, array $args)
    {
        $operator = isset($args[1]) ? $args[1] : '=';

        $this->set($field, $args[0], $operator);
    }
}