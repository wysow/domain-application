<?php

namespace AppBundle\Listener;

use AppBundle\Domain\Identity\ChangedPassword;
use AppBundle\Domain\Identity\ChangedUsername;
use AppBundle\Domain\Identity\UserRegistered;
use Doctrine\DBAL\Connection;
use RayRutjes\DomainFoundation\Contract\ConventionalContractFactory;
use RayRutjes\DomainFoundation\Domain\Event\Event;
use RayRutjes\DomainFoundation\EventBus\Listener\EventListener;

class UserListProjectorListener implements EventListener
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var string
     */
    private $table;

    public function __construct(Connection $connection, $table = 'user_list')
    {
        $this->connection = $connection;
        $this->table      = $table;
    }

    /**
     * @param Event $event
     *
     * @return mixed
     */
    public function handle(Event $event)
    {
        $payload = $event->payload();

        if ($payload instanceof UserRegistered) {
            $this->insertRegisteredUser($payload);
        } elseif ($payload instanceof ChangedPassword) {
            $this->updateUserPassword($payload);
        } elseif ($payload instanceof ChangedUsername) {
            $this->updateUsername($payload);
        }
    }

    private function insertRegisteredUser(UserRegistered $payload)
    {
        $data = [];
        $data['userIdentifier'] = $payload->userIdentifier()->toString();
        $data['username'] = $payload->username()->toString();
        $data['hashedPassword'] = $payload->hashedPassword()->toString();
        $data['at'] = $payload->at()->format('Y-m-d H:i:s');

        $this->connection->insert($this->table, $data);
    }

    private function updateUserPassword(ChangedPassword $payload)
    {
        $identifier = ['userIdentifier' => $payload->userIdentifier()->toString()];

        $data = [];
        $data['hashedPassword'] = $payload->newPassword()->toString();
        $data['at'] = $payload->at()->format('Y-m-d H:i:s');

        $this->connection->update($this->table, $data, $identifier);
    }

    private function updateUsername(ChangedUsername $payload)
    {
        $identifier = ['userIdentifier' => $payload->userIdentifier()->toString()];

        $data = [];
        $data['username'] = $payload->newUsername()->toString();
        $data['at'] = $payload->at()->format('Y-m-d H:i:s');

        $this->connection->update($this->table, $data, $identifier);
    }
}
