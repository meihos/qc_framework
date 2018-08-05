<?php
namespace Core\Events;

/**
 * Class Queue
 * @package Core\Events
 */
class Queue
{
    private $events;

    public function __construct()
    {
        $this->events = [];
    }

    /**
     * @param $name
     * @param $callback
     * @param int $priority
     */
    public function push($name, $callback, $priority = 0)
    {
        $eventArray = [];
        if (isset($this->events[$name])) {
            $eventArray = $this->events[$name];
        }

        $key = (!empty($priority) && is_int($priority)) ? $priority : count($eventArray);

        if (isset($eventArray[$key])) {
            $key++;
        }

        $eventArray[$key] = $callback;
        $this->events[$name] = $eventArray;
    }

    /**
     * @param $name
     * @return array
     */
    public function pop($name)
    {
        if ((!empty($name)) && (isset($this->events[$name]))) {
            return $this->events[$name];
        }

        return [];
    }

    /**
     * @return array
     */
    public function __toArray()
    {
        return $this->events;
    }
}
