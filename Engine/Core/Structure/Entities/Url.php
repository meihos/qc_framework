<?php
namespace Core\Structure\Entities;

/**
 * Class Url
 * @package Core\Structure\Entities
 */
class Url
{
    CONST DEFAULT_PARAMS_SEPARATOR = '&';

    private $schema;
    private $host;
    private $urlPath;
    private $params;
    private $paramsSeparator;

    private $basePath;
    private $route;

    /**
     * @param $schema
     * @param $host
     * @param $urlPath
     */
    public function __construct($schema, $host, $urlPath)
    {
        $this->schema = $schema;
        $this->host = $host;
        $this->urlPath = $urlPath;

        $this->basePath = null;
        $this->route = $urlPath;
        $this->params = [];
        $this->paramsSeparator = self::DEFAULT_PARAMS_SEPARATOR;
    }

    /**
     * @return string
     */
    public function getUrlPath()
    {
        return $this->urlPath;
    }

    /**
     * @return string
     */
    public function getSchema()
    {
        return $this->schema;
    }

    /**
     * @param $schema
     * @return $this
     */
    public function changeSchema($schema)
    {
        if (!empty($schema)) {
            $this->schema = $schema;
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @param $host
     * @return $this
     */
    public function changeHost($host)
    {
        if (!empty($host)) {
            $this->host = $host;
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * @return null
     */
    public function getBasePath()
    {
        return $this->basePath;
    }

    /**
     * @param $basePath
     * @return $this
     */
    public function changeBasePath($basePath)
    {
        $this->basePath = $basePath;
        $this->route = str_replace($this->urlPath, $basePath, '');

        return $this;
    }

    /**
     * @param array $params
     * @return $this
     */
    public function changeParams(array $params)
    {
        $this->params = $params;
        return $this;
    }

    /**
     * @param $separator
     * @return $this
     */
    public function changeParamsSeparator($separator)
    {
        $this->paramsSeparator = $separator;
        return $this;
    }

    /**
     * @return string
     */
    public function getDomain()
    {
        $hostUrl = $this->schema . '://' . $this->host;
        return $hostUrl;
    }

    /**
     * @return string
     */
    public function getFullHost()
    {
        $hostUrl = $this->schema . '://' . $this->host . $this->basePath;
        return $hostUrl;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $hostUrl = $this->getFullHost();
        $hostUrl .= $this->route;

        if (!empty($this->params)) {
            $hostUrl .= implode($this->paramsSeparator, $this->params);
        }

        return $hostUrl;
    }
}