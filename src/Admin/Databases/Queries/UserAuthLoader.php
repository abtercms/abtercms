<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Databases\Queries;

use Opulence\Databases\ConnectionPools\ConnectionPool;
use Opulence\QueryBuilders\MySql\QueryBuilder;

class UserAuthLoader implements AuthLoader
{
    /** @var ConnectionPool */
    protected $connectionPool;

    /**
     * BlockCache constructor.
     *
     * @param ConnectionPool $connectionPool
     */
    public function __construct(ConnectionPool $connectionPool)
    {
        $this->connectionPool = $connectionPool;
    }

    /**
     * @return array|bool
     */
    public function loadAll()
    {
        $query = (new QueryBuilder())
            ->select('u.username AS v0', 'ug.identifier AS v1')
            ->from('users', 'u')
            ->innerJoin('users_user_groups', 'uug', 'uug.user_id = u.id AND uug.deleted = 0')
            ->innerJoin('user_groups', 'ug', 'uug.user_group_id = ug.id AND ug.deleted = 0')
            ->where('u.deleted = 0')
        ;

        $connection = $this->connectionPool->getReadConnection();
        $statement  = $connection->prepare($query->getSql());
        $statement->bindValues($query->getParameters());
        if (!$statement->execute()) {
            return true;
        }

        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }
}
