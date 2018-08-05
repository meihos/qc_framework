<?php
namespace Core\Sql\Query;

/**
 * Class Query
 * @package Core\Sql\Query
 */
class Query
{
    CONST TYPE_SELECT = 'select';
    CONST TYPE_INSERT = 'insert';
    CONST TYPE_UPDATE = 'update';
    CONST TYPE_DELETE = 'delete';

    CONST FETCH_MODE_ASSOC = 'assoc';
    CONST FETCH_MODE_MODEL = 'model';

    protected $type;
    protected $queryString;
    protected $values;
    protected $alias;

    protected $fetchMode;
    protected $fetchArgument;
    protected $success;
    protected $error;

    protected $isCached;
    protected $cacheTTL;


    public function __construct($queryString, $type)
    {
        $this->type = $type;
        $this->values = [];
        $this->queryString = $queryString;
        $this->isCached = false;
        $this->fetchMode = self::FETCH_MODE_ASSOC;
        $this->fetchArgument = null;
        $this->cacheTTL = 0;
        $this->success = false;
        $this->error = null;
    }

    /**
     * @return mixed
     */
    public function getQueryString()
    {
        return $this->queryString;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return mixed
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * @param $queryString
     * @return $this
     */
    public function changeQueryString($queryString)
    {
        $this->queryString = $queryString;
        return $this;
    }

    /**
     * @param $values
     * @return $this
     */
    public function changeValues(array $values)
    {
        $this->values = $values;
        return $this;
    }

    /**
     * @param $key
     * @param $value
     * @return $this
     */
    public function addValue($key, $value)
    {
        $this->values[$key] = $value;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * @param $alias
     * @return $this
     */
    public function changeAlias($alias)
    {
        $this->alias = $alias;
        return $this;
    }

    /**
     * @param $cacheTTL
     * @return $this
     */
    public function markQueryAsCached($cacheTTL)
    {
        $this->isCached = true;
        $this->cacheTTL = $cacheTTL;
        return $this;
    }

    /**
     * @return bool
     */
    public function isCached()
    {
        return $this->isCached;
    }

    /**
     * @return string
     */
    public function getFetchMode()
    {
        return $this->fetchMode;
    }

    /**
     * @return null
     */
    public function getFetchArgument()
    {
        return $this->fetchArgument;
    }

    /**
     * @param $fetchMode
     * @param null $fetchArgument
     * @return bool
     */
    public function setFetchMode($fetchMode, $fetchArgument = null)
    {
        if ($fetchMode == self::FETCH_MODE_ASSOC || $fetchMode == self::FETCH_MODE_MODEL) {
            $this->fetchMode = $fetchMode;
            $this->fetchArgument = $fetchArgument;
            return true;
        }

        return false;
    }

    public function setSuccess($success)
    {
        $this->success = $success;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isSuccess()
    {
        return $this->success;
    }

    public function setError($error)
    {
        $this->error = $error;
        return $this;
    }

    /**
     * @return int
     */
    public function getCacheTTL()
    {
        return $this->cacheTTL;
    }

    public function toLog()
    {
        $logString = '[' . strtoupper($this->type) . "] Execute new query";
        $context = [
            'queryString' => $this->queryString,
            'alias' => $this->alias,
            'fetchMode' => $this->fetchMode,
            'isCached' => $this->isCached(),
            'success' => $this->success,
            'error' => $this->error,
        ];

        return [$logString, $context];
    }

    public function buildQueryHandle()
    {

        return md5($this->queryString . json_encode($this->values));
    }
} 