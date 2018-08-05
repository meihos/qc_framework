<?php

namespace Core\Sql;

use Core\Cache\Adapter\AdapterInterface as CacheAdapterInterface;
use Core\Errors\Errors;
use Core\Log\Adapter\AdapterInterface as LogAdapterInterface;
use Core\Sql\Query\Query;
use Core\Sql\Repository\BuilderInterface;
use Core\Sql\Repository\RepositoryInterface;

/**
 * Class Connection
 * @package Core\Sql
 */
class Connection
{

    /**
     * @var \PDO
     */
    protected $pdoConnection;
    /**
     * @var string
     */
    protected $connectionName;
    /**
     * @var Errors
     */
    protected $errors;
    /**
     * @var CacheAdapterInterface
     */
    protected $cacheAdapter;
    /**
     * @var LogAdapterInterface
     */
    protected $logAdapter;

    /**
     * @param $connectionName
     * @param \PDO $pdoConnection
     * @param Errors $errors
     */
    public function __construct($connectionName, \PDO $pdoConnection, Errors $errors)
    {
        $this->connectionName = $connectionName;
        $this->pdoConnection = $pdoConnection;
        $this->errors = $errors;
        $this->cacheAdapter = null;
        $this->logAdapter = null;
    }

    /**
     * @param CacheAdapterInterface $cacheAdapter
     * @return $this
     */
    public function setCacheAdapter(CacheAdapterInterface $cacheAdapter)
    {
        $this->cacheAdapter = $cacheAdapter;
        return $this;
    }

    /**
     * @param LogAdapterInterface $logAdapter
     * @return $this
     */
    public function setLogAdapter(LogAdapterInterface $logAdapter)
    {
        $this->logAdapter = $logAdapter;
        return $this;
    }

    private function getQueryHandle(Query $query)
    {
        $queryHandle = $query->getAlias();
        if (empty($queryHandle)) {
            $queryHandle = $query->buildQueryHandle();
        }

        return $queryHandle;
    }

    /**
     * @param Query $query
     * @return array|mixed|null
     */
    public function execute(Query $query)
    {
        $resultData = null;
        $isCached = false;
        $queryHandle = $this->getQueryHandle($query);
        $cacheParams = ['connection' => $this->connectionName];

        if ($this->cacheAdapter instanceof CacheAdapterInterface && $query->isCached() && $query->getType() == Query::TYPE_SELECT) {

            $isCached = $this->cacheAdapter->isCached($queryHandle, $cacheParams);
            if ($isCached) {
                $resultData = $this->cacheAdapter->loadCache($queryHandle, $cacheParams);
            }
        }

        if (!$isCached) {
            $sth = $this->pdoConnection->prepare($query->getQueryString());

            foreach ($query->getValues() as $key => $param) {
                switch (strtolower(gettype($param))) {
                    case 'integer' :
                        $typeValue = \PDO::PARAM_INT;
                        break;
                    case 'boolean' :
                        $typeValue = \PDO::PARAM_BOOL;
                        break;
                    case 'null':
                        $typeValue = \PDO::PARAM_NULL;
                        break;
                    default:
                        $typeValue = \PDO::PARAM_STR;
                        break;
                }
                $sth->bindValue($key, $param, $typeValue);
            }

            $query->setSuccess($sth->execute());
            $query->setError($sth->errorInfo()[2]);
            if (($query->getType() == Query::TYPE_SELECT) && ($query->getFetchMode() == Query::FETCH_MODE_ASSOC)) {
                $resultData = $sth->fetchAll(\PDO::FETCH_ASSOC);
            }

            if (($query->getType() == Query::TYPE_SELECT) && ($query->getFetchMode() == Query::FETCH_MODE_MODEL)) {
                $resultData = $sth->fetchAll(\PDO::FETCH_CLASS, $query->getFetchArgument());
            }

            if ($this->cacheAdapter instanceof CacheAdapterInterface && $query->isSuccess() && $query->isCached() && $query->getType() == Query::TYPE_SELECT && $query->getCacheTTL()) {
                $this->cacheAdapter->toCache($queryHandle, $resultData, $cacheParams, $query->getCacheTTL());
            }
        }

        if ($this->logAdapter instanceof LogAdapterInterface) {
            list($logRecord, $context) = $query->toLog();
            $this->logAdapter->info($logRecord, $context);
        }

        return $resultData;
    }

    /**
     * @param $alias
     * @return bool
     */
    public function clearCacheByAlias($alias)
    {
        if (!$this->cacheAdapter instanceof CacheAdapterInterface) {
            $this->errors->error('Incorrect instance for Connection->CacheAdapter', 500);
            return false;
        }

        $cacheParams = ['connection' => $this->connectionName];
        if ($this->cacheAdapter->isCached($alias, $cacheParams)) {
            return $this->cacheAdapter->cleanCache($alias, $cacheParams);
        }

        return false;
    }

    /**
     * @param null $name
     * @return string
     */
    public function getLastInsertId($name = null)
    {
        return $this->pdoConnection->lastInsertId($name);
    }

    /**
     * @return string
     */
    public function getConnectionName()
    {
        return $this->connectionName;
    }

    /**
     * @param $repositoryClass
     * @param $builderClass
     * @return RepositoryInterface|null
     */
    public function buildRepository($repositoryClass, $builderClass)
    {
        if (!class_exists($builderClass)) {
            $this->errors->error('Incorrect  Repository Builder class', 500);
            return null;
        }

        $builderInstance = new $builderClass();
        if (!$builderInstance instanceof BuilderInterface) {
            $this->errors->error('Invalid Repository Builder class', 500);
            return null;
        }

        $repository = $builderInstance->buildRepository($repositoryClass, $this);
        if (!$repository instanceof RepositoryInterface) {
            $this->errors->error('Could not create Repository', 500);
            return null;
        }

        return $repository;
    }
} 