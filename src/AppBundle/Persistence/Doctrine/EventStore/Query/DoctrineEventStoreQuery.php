<?php

namespace AppBundle\Persistence\Doctrine\EventStore\Query;

interface DoctrineEventStoreQuery
{
    /**
     * @return \PDOStatement
     */
    public function prepare();
}
