<?php

namespace Core\Common;

/**
 * Class Session
 * @package Core\Common
 * Singleton
 */
class Session
{
    private $unlockedMode;

    public function __construct()
    {
        session_start();
        $this->unlockedMode = false;
    }

    /**
     * @return $this
     */
    public function lockedMode()
    {
        if ($this->unlockedMode === true) {
            session_start();
        }

        $this->unlockedMode = false;
        return $this;
    }

    /**
     * @return $this
     */
    public function unlockedMode()
    {
        if (($this->unlockedMode === false)) {
            session_write_close();
        }

        $this->unlockedMode = false;
        return $this;
    }

    /**
     * @param $name
     *
     * @return bool
     */
    public function __get($name)
    {
        return (isset($_SESSION[$name])) ? $_SESSION[$name] : null;
    }

    /**
     * @param $name
     * @param $data
     */
    public function __set($name, $data)
    {
        $this->writeData($name, $data);
    }

    private function writeData($name, $data)
    {
        if ($this->unlockedMode) {
            session_start();
        }

        $_SESSION[$name] = $data;

        if ($this->unlockedMode) {
            session_write_close();
        }
    }

    public function __isset($name)
    {
        return isset($_SESSION[$name]);
    }

    /**
     * @return $this
     */
    public function clean()
    {
        $varList = func_get_args();
        foreach ($varList as $var) {
            unset($_SESSION[$var]);
        }

        return $this;
    }

    /**
     * @param $name
     * @param $value
     * @param bool|false $time
     * @param string $path
     * @return $this
     */
    public function setCookie($name, $value, $time = null, $path = '/')
    {
        $time = (!is_null($time)) ? $time : time() + 31536000;
        SetCookie($name, $value, $time, $path);

        return $this;
    }

    /**
     * @param $name
     * @param bool|false $emptyCheck
     * @return bool
     */
    public function isSetup($name, $emptyCheck = false)
    {
        if (empty($name)) {
            return false;
        }

        if ($emptyCheck) {
            if ((isset($_SESSION[$name])) && (!empty($_SESSION[$name]))) {
                return true;
            } else {
                return false;
            }
        } else {
            return isset($_SESSION[$name]);
        }
    }

}