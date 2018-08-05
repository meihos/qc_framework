<?php

namespace Core\Sql;

use Core\Errors\Errors;
use Core\Sql\Repository\BuilderInterface;
use Core\Sql\Repository\RepositoryInterface;
use Core\Structure\Components\LibraryBuilder;
use Core\Structure\Plugins\Collection;
use Core\Structure\Plugins\Settings;

/**
 * Class ConnectionManager
 * @package Core\Sql
 */
class ConnectionManager
{
    use Collection;
    use Settings;

    /**
     * @var Errors
     */
    private $errors;
    private $connectionCollectionName;
    private $pdoCollectionName;

    /**
     * @param Errors $errors
     */
    public function __construct(Errors $errors)
    {
        $this->errors = $errors;
        $this->connectionCollectionName = 'connectionManager_connectionCollection';
        $this->pdoCollectionName = 'connectionManager_pdoCollection';
    }

    /**
     * @param $settings
     * @return $this
     */
    public function setSettings($settings)
    {
        $this->_SettingsInit($settings);
        return $this;
    }

    /**
     * @param $name
     * @param array $settings
     * @return $this
     */
    public function addSettings($name, array $settings)
    {
        $this->_SettingsAddSection($name, $settings);
        return $this;
    }

    private function buildDsn($settings)
    {
        $require = ['engine', 'database', 'host', 'user', 'password', 'port'];
        foreach ($require as $key) {
            if (!isset($settings[$key])) {
                return null;
            }
        }

        $dsn = $settings['engine'] . ':dbname=' . $settings['database'] . ';host=' . $settings['host'] . ';port=' . $settings['port'];
        if (isset($settings['charset'])) {
            $dsn .= ';charset=' . $settings['charset'];
        }

        return $dsn;
    }

    private function __initPdoConnection($settings)
    {
        $dsn = $this->buildDsn($settings);
        $hash = md5($dsn);
        if ($this->_CollectionCheckResource($hash, $this->pdoCollectionName)) {
            return $this->_CollectionGetResource($hash, $this->pdoCollectionName);
        }

        try {
            $pdo = new \PDO($dsn, $settings['user'], $settings['password']);
            $this->_CollectionAddResource($hash, $pdo, $this->pdoCollectionName);
            return $pdo;
        } catch (\PDOException $e) {
            $this->errors->error('Wrong settings for  "' . $settings['host'] . '" [' . $e->getMessage() . ']', 502, true);
        }

        return null;
    }

    /**
     * @param $name
     * @return Connection|null
     */
    public function getConnection($name)
    {
        $settings = $this->_SettingsGetSection($name);
        if (empty($settings)) {
            $this->errors->error('Incorrect settings for Connection [' . $name . ']', true);
        }

        if ($this->_CollectionCheckResource($name, $this->connectionCollectionName)) {
            return $this->_CollectionGetResource($name, $this->connectionCollectionName);
        }

        $dsn = $this->buildDsn($settings);
        $hash = md5($dsn);
        $pdoConnection = null;
        if (!$this->_CollectionCheckResource($hash, $this->pdoCollectionName)) {
            $this->__initPdoConnection($settings);
        }

        $pdoConnection = $this->_CollectionGetResource($hash, $this->pdoCollectionName);
        if (!$pdoConnection instanceof \PDO) {
            $this->errors->error('Can not create PDO Instance for Connection [' . $name . ']', 500);
        }

        $connection = new Connection($name, $pdoConnection, $this->errors);
        $this->_CollectionAddResource($name, $connection, $this->pdoCollectionName);

        return $connection;
    }

    /**
     * @param $name
     * @return $this
     */
    public function cleanConnection($name)
    {
        if ($this->_CollectionCheckResource($name, $this->pdoCollectionName)) {
            $this->_CollectionRemoveResource($name, $this->pdoCollectionName);
        }

        return $this;
    }



}