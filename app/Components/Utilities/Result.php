<?php
/**
 * Created by ASUS.
 * Date: 8/9/2017
 * Time: 3:51 PM
 */
namespace App\Components\Utilities;

use ArrayAccess;

class Result implements ArrayAccess
{
    /**
     * @var array
     */
    private $errorMessages = [];

    /**
     * @var mixed
     */
    private $result;

    /**
     * Returner constructor.
     * @param null $result
     */
    public function __construct($result = null)
    {
        $this->result = $result;
    }

    /**
     * Create new instance.
     * @param mixed $result
     * @return static
     */
    public static function instance ($result = null)
    {
        return new static($result);
    }

    /**
     * Check returner is error.
     * @return bool
     */
    public function isError ()
    {
        return ! empty($this->errorMessages);
    }

    /**
     * Check result have key.
     * @param $key
     * @return bool
     */
    public function has ($key)
    {
        return isset($this->result[$key]);
    }

    /**
     * Check result is empty.
     * @return bool
     */
    public function isEmpty ()
    {
        return empty($this->result);
    }

    /**
     * Set returner as fails.
     * @param null|string|array $message
     * @param string $key
     * @return Result
     */
    public function errors ($message = null, $key = '')
    {
        if (is_array($message)) {
            $this->errorMessages = array_merge($this->errorMessages, $message);

        } else {
            // Add new item.
            if ($key) {
                $this->errorMessages[$key] = $message;

            } else {
                $this->errorMessages[] = (string) $message;
            }
        }

        return $this;
    }

    /**
     * Get error messages/
     * @return array
     */
    public function messages ()
    {
        return $this->errorMessages;
    }

    /**
     * Get error messages/
     * @return array
     */
    public function firstMessage ()
    {
        return reset($this->errorMessages);
    }

    /**
     * Get result.
     * @param string $key
     * @return mixed
     */
    public function result($key = '')
    {
        $output = null;

        if ($key) {
            if (isset($this->result[$key])) {
                $output =  $this->result[$key];
            }
        } else {
            $output = $this->result;
        }

        return $output;
    }

    /**
     * Set result.
     * @param mixed $result
     * @return Result
     */
    public function setResult($result)
    {
        $this->result = $result;

        return $this;
    }

    /**
     * Set result.
     * @param mixed $result
     * @param string $key
     * @return Result
     */
    public function addResult($result, $key = '')
    {
        // Convert exists result to array.
        if (! empty ($this->result) and ! is_array($this->result)) {
            $this->result = [$this->result];
        }

        // Add new item.
        if ($key) {
            $this->result[$key] = $result;
        } else {
            $this->result[] = $result;
        }

        return $this;
    }

    /**
     * Fast get result by key.
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        if (isset($this->result[$name])) {
            return $this->result[$name];
        }

        return null;
    }

    /**
     * Determine if an item exists at an offset.
     * @param  mixed  $key
     * @return bool
     */
    public function offsetExists($key)
    {
        return array_key_exists($key, $this->result);
    }

    /**
     * Get an item at a given offset.
     * @param  mixed  $key
     * @return mixed
     */
    public function offsetGet($key)
    {
        return $this->result[$key];
    }

    /**
     * Set the item at a given offset.
     * @param  mixed  $key
     * @param  mixed  $value
     * @return void
     */
    public function offsetSet($key, $value)
    {
        if (is_null($key)) {
            $this->result[] = $value;
        } else {
            $this->result[$key] = $value;
        }
    }

    /**
     * Unset the item at a given offset.
     * @param  string  $key
     * @return void
     */
    public function offsetUnset($key)
    {
        unset($this->result[$key]);
    }

    /**
     * Convert the collection to its string representation.
     * @return string
     */
    public function __toString()
    {
        return json_encode($this->result);
    }
}