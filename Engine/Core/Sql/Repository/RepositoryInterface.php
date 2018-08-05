<?php
namespace Core\Sql\Repository;

use Core\Sql\Model\Model;

/**
 * Interface RepositoryInterface
 * @package Core\Sql\Repository
 */
interface RepositoryInterface
{
    /**
     * @param Model $model
     * @return bool
     */
    public function save(Model $model);

    /**
     * @param Model $model
     * @return mixed
     */
    public function remove(Model $model);

    /**
     * @param array $identity
     * @param $modelClass
     * @return Model|null
     */
    public function find(array $identity, $modelClass);

    /**
     * @param Filter $filter
     * @return mixed
     */
    public function setFilter(Filter $filter);
} 