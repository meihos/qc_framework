<?php

namespace Applications\Console\Domain\Repository\Nil;

use Core\Sql\Query\Query;
use Libraries\Sql\NilPortugues\Repository as NilBaseRepository;

/**
 * Class NewRepository
 * @package Applications\Console\Domain\Repository\Nil
 */
class NewRepository extends NilBaseRepository
{

    public function getMarketingChannel()
    {
        $select = $this->queryBuilder->select('marketing_clients');
        $select->where()->equals('id', 1);

        $query = $this->createQuery($select);
        $query->markQueryAsCached(300);
        $query->setFetchMode(Query::FETCH_MODE_MODEL, \StdClass::class);

        return $this->connection->execute($query);
    }
}