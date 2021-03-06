<?php

namespace AppBundle\Infrastructure\Persistence;

use AppBundle\Domain\Identity\User;
use AppBundle\Domain\Identity\UserIdentifier;
use AppBundle\Domain\Identity\UserRepository;
use AppBundle\Persistence\Doctrine\EventStore\DoctrineEventStore;
use Doctrine\DBAL\Connection;
use RayRutjes\DomainFoundation\Contract\Contract;
use RayRutjes\DomainFoundation\EventBus\EventBus;
use RayRutjes\DomainFoundation\Repository\AggregateRootRepository;
use RayRutjes\DomainFoundation\UnitOfWork\UnitOfWork;

final class DoctrineUserRepository implements UserRepository
{
    /**
     * @var AggregateRootRepository
     */
    private $repository;

    /**
     * @param UnitOfWork $unitOfWork
     * @param Connection $connection
     */
    public function __construct(UnitOfWork $unitOfWork, Connection $connection, EventBus $eventBus)
    {
        $eventStore = new DoctrineEventStore($connection);

        $aggregateRootType = Contract::createFromClassName(User::class);

        $this->repository = new AggregateRootRepository($unitOfWork, $aggregateRootType, $eventStore, $eventBus);
    }

    /**
     * @param User $user
     */
    public function add(User $user)
    {
        $this->repository->add($user);
    }

    /**
     * @param UserIdentifier $userIdentifier
     * @param null           $expectedVersion
     *
     * @return User
     */
    public function load(UserIdentifier $userIdentifier, $expectedVersion = null)
    {
        return $this->repository->load($userIdentifier, $expectedVersion);
    }
}
