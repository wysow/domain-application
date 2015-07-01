<?php

namespace AppBundle\Persistence\Doctrine\EventStore\Query;

use Doctrine\DBAL\Connection;

final class InsertQuery extends AbstractQuery
{
    protected $sql = <<<MYSQL
INSERT INTO `%s` (
    `aggregate_id`,
    `aggregate_type`,
    `aggregate_version`,
    `event_id`,
    `event_payload`,
    `event_payload_type`,
    `event_metadata`,
    `event_metadata_type`,
    `commit_id`,
    `committed_at`
) VALUES (
    :aggregate_id,
    :aggregate_type,
    :aggregate_version,
    :event_id,
    :event_payload,
    :event_payload_type,
    :event_metadata,
    :event_metadata_type,
    :commit_id,
    :committed_at
);
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
