<?php
namespace Core\Events;

/**
 * Class EventManager
 * @package Core\Events
 */
class EventManager
{
    private $events;
    private $triggeredActions;


    public function __construct()
    {
        $this->events = new Queue();
    }

    /**
     * @param $name
     * @param $callback
     * @param int $priority
     */
    public function attach($name, $callback, $priority = 0)
    {
        $this->events->push($name, $callback, $priority);
    }

    /**
     * @param $name
     * @param array $params
     * @param null $afterTrigger
     * @param null $parameterName
     */
    public function trigger($name, $params = array(), $afterTrigger = null, $parameterName = null)
    {
        $listeners = $this->events->pop($name);
        $event = new Event($name, $params);
        $return = null;
        foreach ($listeners as $listener) {
            $this->addToTriggeredActions($name, $listener);
            if (is_callable($listener)) {
                call_user_func($listener, $event);
            }
        }

        if (is_callable($afterTrigger)) {
            $params = (is_null($parameterName)) ? $event->getParams() : $event->getParameter($parameterName, null);
            call_user_func($afterTrigger, $params);
        }
    }

    private function addToTriggeredActions($name, $listener)
    {
        if ((!empty($name)) && (!empty($listener))) {
            if (!isset($this->triggeredActions[$name])) {
                $this->triggeredActions[$name] = [];
            }

            $structure = ['object' => '', 'method' => ''];

            if (is_object($listener[0])) {
                $structure['object'] = get_class($listener[0]);
            } else {
                $structure['object'] = $listener;
            }

            if (isset($listener[1])) {
                $structure['method'] = $listener[1];
            }


            if (!in_array($structure, $this->triggeredActions[$name])) {
                $this->triggeredActions[$name][] = $structure;
            }
        }
    }

    /**
     * @return Queue
     */
    public function getQueue()
    {
        return $this->events;
    }

}