<?php
namespace Core\Events;

/**
 * Class Event
 * @package Core\Events
 */
class Event
{
    private $name;
    private $params;

    /**
     * @param $name
     * @param array $params
     */
    public function __construct($name, $params = array())
    {
        $this->name = $name;
        $this->params = $params;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @param $name
     * @param mixed|null $default
     * @return mixed|null
     */
    public function getParameter($name, $default = null)
    {
        $return = (isset($this->params[$name])) ? $this->params[$name] : $default;
        return $return;
    }

    /**
     * @param $params
     * @return $this
     */
    public function updateParams($params)
    {
        $this->params = $params;
        return $this;
    }

    /**
     * @param $name
     * @param $value
     * @return $this
     */
    public function updateParameter($name, $value)
    {
        $this->params[$name] = $value;
        return $this;
    }
}