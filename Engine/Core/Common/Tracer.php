<?php
namespace Core\Common;

/**
 * Class Tracer
 * @package Core\Common
 * Singleton
 */
class Tracer
{

    private $traceLog;
    private $startTime;

    public function __construct()
    {
        $this->traceLog = [];
        $this->startTime = microtime(true);
    }

    /**
     * @param $startTime
     * @return $this
     */
    public function setStartTime($startTime)
    {
        if (!empty($startTime)) {
            $this->startTime = $startTime;
        }

        return $this;
    }

    /**
     * @param $name
     * @param int $time
     * @return $this
     */
    public function setPoint($name, $time = 0)
    {
        if (!empty($name)) {
            $time = (!empty($time)) ? $time : microtime(true);
            $this->traceLog[] = [
                'name' => $name,
                'time' => $time,
                'diff' => $time - $this->startTime,
            ];
        }

        return $this;
    }

    public function showTracing($delimiter = '<br>')
    {
        if ((!empty($this->traceLog)) && (is_array($this->traceLog))) {
            foreach ($this->traceLog as $position => $info) {
                echo ++$position . '. Point [' . $info['name'] . '] at time ' . $info['time'] . ' [Diff:' . $info['diff'] . ']';
                echo $delimiter;
            }
        }
    }

    /**
     * @return array
     */
    public function getTracerLog()
    {
        return $this->traceLog;
    }
}