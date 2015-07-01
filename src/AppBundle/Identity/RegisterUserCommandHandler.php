<?php

namespace AppBundle\Identity;

use AppBundle\Domain\Identity\Password;
use AppBundle\Domain\Identity\RegisterUserService;
use AppBundle\Domain\Identity\User;
use AppBundle\Domain\Identity\UserRepository;
use AppBundle\Domain\Identity\Username;
use RayRutjes\DomainFoundation\Command\Command;
use RayRutjes\DomainFoundation\Command\Handler\CommandHandler;

final class RegisterUserCommandHandler implements CommandHandler
{
    /**
     * @var RegisterUserService
     */
    private $registerUserService;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @param RegisterUserService $registerUserService
     * @param UserRepository      $userRepository
     */
    public function __construct(RegisterUserService $registerUserService, UserRepository $userRepository)
    {
        $this->registerUserService = $registerUserService;
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

        if($payload instanceof RegisterUserCommand) {
            $this->handleUserRegistration($payload);
        }
    }

    /**
     * @param  RegisterUserCommand $command
     *
     * @return User                $user
     */
    private function handleUserRegistration(RegisterUserCommand $command)
    {
        $username = new Username($command->username());
        $password = new Password($command->password());

        $user = $this->registerUserService->registerUser($username, $password);

        $this->userRepository->add($user);

        return $user;
    }
}
