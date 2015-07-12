<?php

namespace AppBundle\Persistence\Doctrine\EventStore;

use AppBundle\Persistence\Doctrine\EventStore\Query\CreateQuery;
use AppBundle\Persistence\Doctrine\EventStore\Query\InsertQuery;
use AppBundle\Persistence\Doctrine\EventStore\Query\SelectQuery;
use Doctrine\DBAL\Connection;
use RayRutjes\DomainFoundation\Contract\Contract;
use RayRutjes\DomainFoundation\Domain\AggregateRoot\AggregateRootIdentifier;
use RayRutjes\DomainFoundation\Domain\Event\Serializer\CompositeEventSerializer;
use RayRutjes\DomainFoundation\Domain\Event\Serializer\EventSerializer;
use RayRutjes\DomainFoundation\Domain\Event\Stream\EventStream;
use RayRutjes\DomainFoundation\EventStore\CommitIdentifier;
use RayRutjes\DomainFoundation\EventStore\EventStore;
use RayRutjes\DomainFoundation\Persistence\Pdo\EventStore\PdoReadRecordEventStream;
use RayRutjes\DomainFoundation\Serializer\JsonSerializer;

final class DoctrineEventStore implements EventStore
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
     * @var EventSerializer
     */
    private $eventSerializer;

    /**
     * @param Connection               $connection
     * @param string                   $tableName
     * @param EventSerializer          $eventSerializer
     */
    public function __construct(
        Connection $connection,
        $tableName = 'events',
        EventSerializer $eventSerializer = null
    ) {
        $this->connection               = $connection;
        $this->tableName                = $tableName;
        $this->eventSerializer          = null === $eventSerializer ? new CompositeEventSerializer(new JsonSerializer()) : $eventSerializer;
        $this->insertQuery              = new InsertQuery($connection, $tableName);
        $this->selectQuery              = new SelectQuery($connection, $tableName);
        $this->createQuery              = new CreateQuery($connection, $tableName);
    }

    /**
     * @param Contract    $aggregateType
     * @param EventStream $eventStream
     *
     * @throws \Exception
     */
    public function append(Contract $aggregateType, EventStream $eventStream)
    {
        $statement = $this->insertQuery->prepare();

        $commitIdentifier = CommitIdentifier::generate()->toString();
        $committedAt = new \DateTime();

        while ($eventStream->hasNext()) {
            $event = $eventStream->next();

            $statement->bindValue(':aggregate_id', $event->aggregateRootIdentifier()->toString());
            $statement->bindValue(':aggregate_type', $aggregateType->toString());
            $statement->bindValue(':aggregate_version', $event->sequenceNumber());
            $statement->bindValue(':event_id', $event->identifier()->toString());
            $statement->bindValue(':event_payload', $this->eventSerializer->serializePayload($event));
            $statement->bindValue(':event_payload_type', $event->payloadType()->toString());
            $statement->bindValue(':event_metadata', $this->eventSerializer->serializeMetadata($event));
            $statement->bindValue(':event_metadata_type', $event->metadataType()->toString());
            $statement->bindValue(':commit_id', $commitIdentifier);
            $statement->bindValue(':committed_at', $committedAt->format('UTC'));

            $statement->execute();
            $statement->closeCursor();
        }
    }

    /**
     * @param Contract                $aggregateType
     * @param AggregateRootIdentifier $aggregateRootIdentifier
     *
     * @return EventStream
     */
    public function read(Contract $aggregateType, AggregateRootIdentifier $aggregateRootIdentifier)
    {
        $statement = $this->selectQuery->prepare();

        $statement->bindValue(':aggregate_id', $aggregateRootIdentifier->toString());
        $statement->bindValue(':aggregate_type', $aggregateType->toString());

        $statement->execute();

        $records = $statement->fetchAll();
        $statement->closeCursor();

        return new PdoReadRecordEventStream(
            $aggregateRootIdentifier,
            $records,
            $this->eventSerializer
        );
    }

    /**
     * Create the database table.
     */
    public function createTable()
    {
        $statement = $this->createQuery->prepare();
        $statement->execute();
        $statement->closeCursor();
    }
}
