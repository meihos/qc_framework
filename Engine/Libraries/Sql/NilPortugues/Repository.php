<?php
namespace Libraries\Sql\NilPortugues;

use Core\Sql\Connection;
use Core\Sql\Model\Model;
use Core\Sql\Query\Query;
use Core\Sql\Repository\Filter;
use Core\Sql\Repository\RepositoryInterface;
use NilPortugues\Sql\QueryBuilder\Builder\GenericBuilder;
use NilPortugues\Sql\QueryBuilder\Manipulation\QueryInterface;
use NilPortugues\Sql\QueryBuilder\Manipulation\Select;

/**
 * Class BaseRepository
 * @package Libraries\Sql\NilPortugues
 */
class Repository implements RepositoryInterface
{
    /**
     * @var GenericBuilder
     */
    protected $queryBuilder;
    /**
     * @var Connection
     */
    protected $connection;
    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @param GenericBuilder $builder
     * @param Connection $connection
     */
    public function __construct(GenericBuilder $builder, Connection $connection)
    {
        $this->queryBuilder = $builder;
        $this->connection = $connection;
        $this->filter = null;
    }

    protected function createQuery(QueryInterface $baseQuery)
    {
        switch (strtolower($baseQuery->partName())) {
            case 'select':
                $type = Query::TYPE_SELECT;
                break;
            case 'delete':
                $type = Query::TYPE_DELETE;
                break;
            case 'insert':
                $type = Query::TYPE_INSERT;
                break;
            case 'update':
                $type = Query::TYPE_UPDATE;
                break;
            default:
                $type = Query::TYPE_SELECT;
                break;
        }

        $query = new Query($this->queryBuilder->write($baseQuery), $type);

        $values = $this->queryBuilder->getValues();
        foreach ($values as $key => $value) {
            $query->addValue($key, $value);
        }

        return $query;
    }

    protected function executeFilterToSQl($permissions = [], QueryInterface $query)
    {
        if (($this->filter instanceof Filter) && ($query instanceof Select)) {

            if (!in_array('limit', $permissions)) {
                $filter = $this->filter->getLimitFilter();
                if (!empty($filter)) {
                    $start = (isset($filter['start'])) ? $filter['start'] : 0;
                    $query->limit($start, $filter['count']);
                }
            }

            if (!in_array('order', $permissions)) {
                $filter = $this->filter->getOrderFilter();
                if (!empty($filter)) {
                    foreach ($filter as $order) {
                        $query->orderBy($order['column'], $order['direction']);
                    }
                }
            }

            if (!in_array('where', $permissions)) {
                $filter = $this->filter->getWhereFilter();
                if (!empty($filter)) {
                    foreach ($filter as $where) {
                        switch ($where['method']) {
                            case 'eq' :
                                $query->where($where['connector'])->eq($where['column'], $where['value']);
                                break;
                            case 'like' :
                                $query->where($where['connector'])->like($where['column'], $where['value']);
                                break;
                            case 'in' :
                                $query->where($where['connector'])->in($where['column'], $where['value']);
                                break;
                            case 'notIn':
                                $query->where($where['connector'])->notIn($where['column'], $where['value']);
                                break;
                        }
                    }
                }
            }

        }

        $this->filter = null;
    }

    /**
     * @param Model $model
     * @return bool
     */
    public function save(Model $model)
    {
        $sqlValues = $model->fieldsToSql();
        if (empty($sqlValues['keys'])) {
            return false;
        }

        $select = $this->queryBuilder->select($model->__table());
        foreach ($sqlValues['keys'] as $pKey => $pKeyValue) {
            $select->where()->equals($pKey, $pKeyValue);
        }
        $select->limit(0, 1);

        $query = $this->createQuery($select);
        $query->setFetchMode(Query::FETCH_MODE_ASSOC);
        $data = $this->connection->execute($query);

        if (empty($data) || !is_array($data)) {
            $fields = array_merge($sqlValues['fields'], $sqlValues['keys']);
            $saveSelect = $this->queryBuilder->insert($model->__table(), $fields);
        } else {
            $saveSelect = $this->queryBuilder->update($model->__table(), $sqlValues['fields']);
            foreach ($sqlValues['keys'] as $field => $value) {
                $saveSelect->where()->equals($field, $value);
            }
        }

        $query = $this->createQuery($saveSelect);
        $this->connection->execute($query);

        return true;
    }

    /**
     * @param Model $model
     * @return mixed
     */
    public function remove(Model $model)
    {
        $sqlValues = $model->fieldsToSql();
        if (empty($sqlValues['keys'])) {
            return false;
        }

        $delete = $this->queryBuilder->delete($model->__table());
        foreach ($sqlValues['keys'] as $pKey => $pKeyValue) {
            $delete->where()->equals($pKey, $pKeyValue);
        }

        $query = $this->createQuery($delete);
        $this->connection->execute($query);

        return true;
    }

    /**
     * @param array $identity
     * @param $modelClass
     * @return null
     */
    public function find(array $identity, $modelClass)
    {
        $model = new $modelClass();
        if (!$model instanceof Model) {
            return null;
        }

        $modelFields = $model->__fields();
        $select = $this->queryBuilder->select($model->__table());
        foreach ($identity as $field => $fValue) {
            if (!isset($modelFields[$field])) {
                continue;
            }

            $fieldSql = $modelFields[$field];
            $select->where()->equals($fieldSql, $fValue);
        }
        $select->limit(0, 1);

        $query = $this->createQuery($select);
        $query->setFetchMode(Query::FETCH_MODE_ASSOC);
        $data = $this->connection->execute($query);

        if (empty($data) || count($data) != 1) {
            return null;
        }

        $model->fieldsFromSql(current($data));
        return $model;
    }

    /**
     * @param Filter $filter
     * @return mixed
     */
    public function setFilter(Filter $filter)
    {
        $this->filter = $filter;
        return $this;
    }

}