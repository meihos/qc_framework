<?php
namespace Core\Structure\Entities;

/**
 * Class Variable
 * @package Core\Structure\Entities
 */
class Variable
{

    private $value;

    /**
     * @param null $value
     */
    public function __construct($value = null)
    {
        $this->value = $value;
    }

    /**
     * @return bool
     */
    public function toBoolean()
    {
        return (bool)$this->value;
    }

    /**
     * @return float
     */
    public function toFloat()
    {
        return (float)$this->value;
    }

    /**
     * @return int
     */
    public function toInt()
    {
        return (int)$this->value;
    }

    /**
     * @return array|null
     */
    public function toArray()
    {
        if (is_array($this->value)) {
            return $this->value;
        }

        return (array)$this->value;
    }

    /**
     * @return string
     */
    public function toString()
    {

        if (is_array($this->value)) {
            return print_r($this->value, true);
        }

        if (is_object($this->value)) {
            return 'Object [' . get_class($this->value) . ']';
        }
        return (string)$this->value;
    }

    /**
     * @return object
     */
    public function toObject()
    {
        return (object)$this->value;
    }

    /**
     * @return mixed|null
     */
    public function get()
    {
        return $this->value;
    }

    /**
     * @param $value
     * @return $this
     */
    public function set($value)
    {
        $this->value = $value;
        return $this;
    }
}