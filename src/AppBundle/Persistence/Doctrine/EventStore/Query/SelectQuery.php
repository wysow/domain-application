<?php

namespace AppBundle\Persistence\Doctrine\EventStore\Query;

use Doctrine\DBAL\Connection;

final class SelectQuery extends AbstractQuery
{
    protected $sql = <<<MYSQL
SELECT * FROM `%s`
WHERE `aggregate_id` = :aggregate_id
AND `aggregate_type` = :aggregate_type
ORDER BY `aggregate_version`;
MYSQL;

    /**
     * @param Connection $connection
     * @param string     $tableName
     */
    public function __construct(Connection $connection, $tableName)
    {
        parent::__construct($connection, $tableName, $this->sql);
    }
}
