<?php

namespace AppBundle\Identity;

use RayRutjes\DomainFoundation\Command\Command;
use RayRutjes\DomainFoundation\Command\Handler\CommandHandler;
use AppBundle\Domain\Identity\UserIdentifier;
use AppBundle\Domain\Identity\UserRepository;
use AppBundle\Domain\Identity\Username;

final class ChangeUsernameCommandHandler implements CommandHandler
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param Command $command
     *
     * @return mixed
     */
    public function handle(Command $command)
    {
        $payload = $command->payload();

        if($payload instanceof ChangeUsernameCommand) {
            $this->handleUsernameChange($payload);
        }
    }

    /**
     * @param ChangeUsernameCommand $command
     *
     * @return User
     */
    private function handleUsernameChange(ChangeUsernameCommand $command)
    {
        $userIdentifier = new UserIdentifier($command->userIdentifier());
        $newUsername = new Username($command->newUsername());

        $user = $this->userRepository->load($userIdentifier);
        $user->changeUsername($newUsername);

        return $user;
    }
}
