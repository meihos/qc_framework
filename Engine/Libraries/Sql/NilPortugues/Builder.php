<?php

namespace Libraries\Sql\NilPortugues;

use Core\Sql\Connection;
use Core\Sql\Repository\BuilderInterface;
use NilPortugues\Sql\QueryBuilder\Builder\GenericBuilder;

/**
 * Class Factory
 * @package Libraries\Sql\NilPortugues
 */
class Builder implements BuilderInterface
{

    /**
     * @param $repositoryClass
     * @param Connection $connection
     * @return Repository|null
     */
    public function buildRepository($repositoryClass, Connection $connection)
    {
        $repository = null;
        if (!class_exists($repositoryClass)) {
            return $repository;
        }

        if (!$connection instanceof Connection) {
            return $repository;
        }

        $genericBuilder = new GenericBuilder();
        $resource = new $repositoryClass($genericBuilder, $connection);
        if (!$resource instanceof Repository) {
            return $repository;
        }

        $repository = $resource;

        return $repository;
    }
}