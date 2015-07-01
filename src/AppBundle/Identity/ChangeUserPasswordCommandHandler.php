<?php

namespace AppBundle\Identity;

use RayRutjes\DomainFoundation\Command\Command;
use RayRutjes\DomainFoundation\Command\Handler\CommandHandler;
use AppBundle\Domain\Identity\Password;
use AppBundle\Domain\Identity\PasswordHashingService;
use AppBundle\Domain\Identity\UserIdentifier;
use AppBundle\Domain\Identity\UserRepository;

final class ChangeUserPasswordCommandHandler implements CommandHandler
{
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var PasswordHashingService
     */
    private $passwordHashingService;

    /**
     * @param UserRepository         $userRepository
     * @param PasswordHashingService $passwordHashingService
     */
    public function __construct(UserRepository $userRepository, PasswordHashingService $passwordHashingService)
    {
        $this->userRepository = $userRepository;
        $this->passwordHashingService = $passwordHashingService;
    }

    /**
     * @param Command $command
     *
     * @return mixed
     */
    public function handle(Command $command)
    {
        $payload = $command->payload();

        if($payload instanceof ChangeUserPasswordCommand) {
            $this->handleUserPasswordChange($payload);
        }
    }

    /**
     * @param ChangeUserPasswordCommand $command
     *
     * @return User
     */
    private function handleUserPasswordChange(ChangeUserPasswordCommand $command)
    {
        $userIdentifier = new UserIdentifier($command->userIdentifier());
        $newPassword = new Password($command->newPassword());

        $hashedPassword = $this->passwordHashingService->hashPassword($newPassword);

        $user = $this->userRepository->load($userIdentifier);
        $user->changePassword($hashedPassword);

        return $user;
    }
}
