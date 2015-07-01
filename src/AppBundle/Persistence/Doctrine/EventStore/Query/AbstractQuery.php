<?php

namespace AppBundle\Persistence\Doctrine\EventStore\Query;

use Doctrine\DBAL\Connection;

abstract class AbstractQuery implements DoctrineEventStoreQuery
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var string
     */
    private $tableName;

    /**
     * @var string
     */
    private $sqlQueryString;

    /**
     * @param Connection $connection
     * @param string     $tableName
     * @param string     $sql
     */
    public function __construct(Connection $connection, $tableName, $sql)
    {
        $this->connection = $connection;

        if (!is_string($tableName)) {
            throw new \LogicException('Table name must be a string.');
        }

        if (!is_string($sql)) {
            throw new \LogicException('Sql must be a string.');
        }

        $this->tableName      = $tableName;
        $this->sqlQueryString = $sql;
    }

    /**
     * @return \PDOStatement
     */
    public function prepare()
    {
        return $this->connection->prepare($this->getSql());
    }

    /**
     * @return sql
     */
    protected function getSql()
    {
        return sprintf($this->sqlQueryString, $this->tableName);
    }
}
