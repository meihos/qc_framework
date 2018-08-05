<?php
namespace Core\Errors;

use Core\Common\Request;
use Core\Log\Adapter\AdapterInterface;

/**
 * Class Errors
 * @package Core\Common
 */
class Errors
{
    private $errorCodes = [
        400 => 'Bad Request',
        401 => 'Unauthorized',
        403 => 'Forbidden',
        404 => 'Not found',
        429 => 'Too many connections',
        500 => 'Internal server error',
        502 => 'Bad Gateway',
        504 => 'Gateway Timeout',
    ];

    private $errorCodesStrategy;
    private $request;
    private $logAdapter;


    /**
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->errorCodesStrategy = [];
        $this->logAdapter = null;
    }

    /**
     * @param array $strategy
     * @return $this
     */
    public function setErrorStrategy(array $strategy)
    {
        foreach ($strategy as $k => $row) {
            if (isset($this->errorCodes[$k])) {
                $this->errorCodesStrategy['error_' . $k] = $row;
            }
        }

        return $this;
    }

    /**
     * @param AdapterInterface $logAdapter
     * @return $this
     */
    public function setLogger(AdapterInterface $logAdapter)
    {
        $this->logAdapter = $logAdapter;
        return $this;
    }

    /**
     * @param $message
     * @param int $code
     * @param bool $terminate
     * @return $this
     */
    public function error($message, $code = 500, $terminate = false)
    {
        $code = intval($code);
        $this->addLogRecord($code, $message);

        if (($terminate)) {
            $code = (array_key_exists($code, $this->errorCodes)) ? $code : 500;
            if (!$this->request->isConsole()) {
                $this->throwCodeHeader($code);
            }

            die();
        }

        return $this;
    }

    /**
     * @param $code
     * @param $text
     */
    private function addLogRecord($code, $text)
    {
        if (!$this->logAdapter instanceof AdapterInterface) {
            return;
        }

        $key = 'error_' . $code;
        $level = $this->logAdapter->defaultLevel();
        if (array_key_exists($key, $this->errorCodesStrategy)) {
            $level = $this->errorCodesStrategy[$key];
        }

        $this->logAdapter->log($level, $text, ['exception' => new \Exception($text, $code)]);
    }


    /**
     * @param $code
     * @return null
     */
    private function throwCodeHeader($code)
    {
        header('HTTP/1.1 ' . $code . ' ' . $this->errorCodes[$code], true, $code);
    }

}