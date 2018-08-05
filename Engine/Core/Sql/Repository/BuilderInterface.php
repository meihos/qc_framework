<?php

namespace Core\Sql\Repository;

use Core\Sql\Connection;

/**
 * Interface BuilderInterface
 * @package Core\Sql\Repository
 */
interface BuilderInterface
{
    /**
     * @param $repository
     * @param Connection $connection
     * @return mixed
     */
    public function buildRepository($repository, Connection $connection);
}