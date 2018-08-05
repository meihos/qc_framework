<?php
namespace Core\Common;

/**
 * Class Request
 * @package Core\Common
 * Singleton
 */
class Request
{
    private $request;
    private $info;

    public function __construct()
    {
        $this->request = [
            'buffer' => $_REQUEST,
            'post' => $_POST,
            'get' => $_GET,
            'request' => $_REQUEST,
            'cookie' => $_COOKIE,
            'files' => $_FILES
        ];
        $this->info = $_SERVER;
    }

    /**
     * @param array|string $name
     * @param bool|false $emptyCheck
     * @param string|null $type
     * @return bool
     */
    public function isSend($name, $emptyCheck = false, $type = null)
    {
        if (empty($name)) {
            return false;
        }

        $section = (!empty($type)) ? strtolower($type) : 'buffer';

        if (is_array($name)) {

            foreach ($name as $directName) {
                if (!isset($this->request[$section][$directName])) {
                    return false;
                }

                if ($emptyCheck) {

                    if (($section != 'files') && (empty($this->request[$section][$directName])) ||
                        (($section == 'files') && (empty($this->request[$section][$directName]['name'])))
                    ) {
                        return false;
                    }
                }
            }

            return true;

        } else {

            if ((is_string($name)) && (isset($this->request[$section][$name]))) {
                if ($emptyCheck) {

                    if ($section != 'files') {
                        return !empty($this->request[$section][$name]);
                    } else {
                        return !empty($this->request[$section][$name]['name']);
                    }
                }

                return true;
            }
        }
        return false;
    }


    /**
     * @return bool
     */
    public function isAjax()
    {
        $request = $this->getRequestInfo('HTTP_X_REQUESTED_WITH');

        return ($request && (strtolower($request) == 'xmlhttprequest'));

    }

    /**
     * @return bool
     */
    public function isHttps()
    {
        $isHttps = false;
        if ($this->getRequestInfo('HTTPS') == 'on') {
            $isHttps = true;
        } elseif ($this->getRequestInfo('HTTP_X_FORWARDED_PROTO') == 'https' || $this->getRequestInfo('HTTP_X_FORWARDED_SSL') == 'on') {
            $isHttps = true;
        }

        return $isHttps;
    }

    /**
     * @return bool
     */
    public function isConsole()
    {
        return ($this->getRequestInfo('argc') > 0);
    }

    /**
     * @param $name
     * @return mixed|null
     */
    public function getRequestInfo($name)
    {
        if ((!empty($name)) && (isset($this->info[$name]))) {
            return $this->info[$name];
        }

        return null;
    }

    /**
     * @param $type
     * @return array|null
     */
    public function getRequestData($type = null)
    {
        $type = (!empty($type)) ? strtolower($type) : 'buffer';

        if (isset($this->request[$type])) {
            return $this->request[$type];
        }

        return null;
    }

    /**
     * @param $name
     * @return array
     */
    public function getFiles($name)
    {
        $return = [];
        $files = $this->getParameter($name, [], 'files');

        if (!empty($files)) {
            if (($count = count($files['name'])) == 1) {
                return $files;
            }

            for ($i = 0; $i < $count; $i++) {
                $file = [];
                foreach ($files as $k => $row) {
                    if (isset($row[$i])) {
                        $file[$k] = $row[$i];
                    }
                }

                if (!empty($file)) {
                    $return[] = $file;
                }
            }
        }

        return $return;
    }

    /**
     * @param $name
     * @param null $default
     * @param null $type
     * @param null $validate
     * @return mixed|null
     */
    public function getParameter($name, $default = null, $type = null, $validate = null)
    {

        $section = (!empty($type)) ? strtolower($type) : 'buffer';
        $retVal = (isset($this->request[$section][$name]) && (!empty($this->request[$section][$name])))
            ? $this->request[$section][$name] : $default;
        $retVal = (is_array($retVal)) ? $retVal : trim($retVal);

        if (!is_null($validate) && (!filter_var($retVal, $validate))) {
            $retVal = $default;
        }

        return $retVal;
    }

    /**
     * @param string $name
     * @param mixed $value
     * @param string|null $type
     * @return $this
     */
    public function rewriteParameter($name, $value, $type = null)
    {
        $section = 'buffer';
        if (isset($this->request[$section][$name])) {
            $this->request[$section][$name] = $value;
        }

        if (!empty($type)) {
            $section = strtolower($type);
            if (isset($this->request[$section][$name])) {
                $this->request[$section][$name] = $value;
            }
        }

        return $this;
    }

    /**
     * @param null $type
     * @return string
     */
    public function getRequestHash($type = null)
    {
        $type = (!empty($type)) ? strtolower($type) : 'buffer';
        $vars = $this->request[$type];
        return md5(http_build_query($vars));
    }
}