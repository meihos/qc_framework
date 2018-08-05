<?php
namespace Core\Guard;

use Core\Common\Request;
use Core\Structure\Plugins\HashTable;

/**
 * Class Guard
 * @package Core\Guard
 */
class Guard
{
    use HashTable;

    CONST PER_SECONDS = 1;
    CONST PER_MINUTE = 2;
    CONST PER_HOUR = 3;
    CONST PER_DAY = 4;

    /**
     * @var Request
     */
    private $request;
    private $isEnable;
    private $callback;

    /**
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->isEnable = false;
        $this->callback = null;
    }

    /**
     * @return $this
     */
    public function enable()
    {
        $this->isEnable = true;
        return $this;
    }

    /**
     * @return $this
     */
    public function disable()
    {
        $this->isEnable = false;
        return $this;
    }

    /**
     * @param callable $callback
     * @return $this
     */
    public function setCallback(callable $callback)
    {
        $this->callback = $callback;
        return $this;
    }

    /**
     * @param $identity
     * @param int $mode
     * @param int $requests
     * @param int $blockTime
     * @return bool
     */
    public function checkActivity($identity, $mode = 3, $requests = 100, $blockTime = 60)
    {
        $blockAction = false;
        if (!$this->_HashTableIsInitAdapter()) {
            return $blockAction;
        }

        if (!$this->_HashTableCheckRecord($identity)) {
            $structure = [
                'identity' => $identity,
                'startTime' => microtime(true),
                'lastActionTime' => microtime(true),
                'IP' => [$this->request->getRequestInfo('REMOTE_ADDR')],
                'count' => 1,
                'isBlocked' => false,
                'unBlockTime' => 0,
            ];

            $this->_HashTableAddRecord($identity, $structure, true);
        }

        $structure = $this->_HashTableGetRecord($identity, null);

        if (!is_null($structure)) {

            $structure = $this->checkBlockTime($structure);

            switch ($mode) {
                case self::PER_DAY:
                    $diff = 86400;
                    $clean = true;
                    break;
                case self::PER_HOUR:
                    $diff = 3600;
                    $clean = true;
                    break;
                case self::PER_MINUTE:
                    $diff = 60;
                    $clean = false;
                    break;
                case self::PER_SECONDS:
                    $diff = 3;
                    $structure['startTime'] = microtime(true);
                    $clean = false;
                    break;
                default :
                    $clean = true;
                    $diff = 86400;
                    break;
            }

            $structure = $this->checkActivityForTimeLimit($structure, $diff, $requests, $blockTime, $clean);
            $this->_HashTableAddRecord($identity, $structure, true);
            $blockAction = $structure['isBlocked'];

            if (($blockAction) && (is_callable($this->callback))) {
                call_user_func($this->callback, $structure);
            }
        }

        return $blockAction;
    }

    private function checkBlockTime(array $structure)
    {
        $time = microtime(true);
        if (($structure['isBlocked']) && ($time > $structure['unBlockTime'])) {
            $structure['count'] = 0;
            $structure['startTime'] = $time;
            $structure['isBlocked'] = false;
            $structure['unBlockTime'] = 0;
        }
        return $structure;
    }

    private function checkActivityForTimeLimit(array $structure, $diff, $requests, $blockTime, $longSession = false)
    {
        if (isset($structure['count']) && isset($structure['startTime']) && isset($structure['isBlocked'])) {

            $time = microtime(true);
            $structure['lastActionTime'] = $time;
            $structure['count']++;

            if (($structure['count'] >= $requests) && (($time - $structure['startTime']) < $diff)) {
                $structure['isBlocked'] = true;
                $structure['unBlockTime'] = time() + $blockTime;
            }

            if (($longSession) && (!$structure['isBlocked'])) {
                $part = $diff / 10;
                if (($time - $structure['lastActionTime']) > $part) {
                    $structure['startTime'] = $time;
                    $structure['count'] = 1;
                }
            }
        }

        $ip = $this->request->getRequestInfo('REMOTE_ADDR');

        if (!in_array($ip, $structure['IP'])) {
            $structure['IP'][] = $ip;
        }

        return $structure;
    }
} 