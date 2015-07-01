<?php

namespace AppBundle\Command;

use AppBundle\Identity\RegisterUserCommand as DomainCommand;
use Hackzilla\PasswordGenerator\Generator\ComputerPasswordGenerator;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RegisterUserCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:user:register')
            ->setDescription('Register a new user')
            ->addArgument(
                'username',
                InputArgument::REQUIRED,
                'Which username?'
            )
            ->addArgument(
                'password',
                InputArgument::OPTIONAL,
                'Set a password, if not set a generated password will be used.'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $username = $input->getArgument('username');

        if ($input->getArgument('password')) {
            $password = $input->getArgument('password');
        } else {
            $generator = new ComputerPasswordGenerator();

            $generator
              ->setOptionValue(ComputerPasswordGenerator::OPTION_UPPER_CASE, true)
              ->setOptionValue(ComputerPasswordGenerator::OPTION_LOWER_CASE, true)
              ->setOptionValue(ComputerPasswordGenerator::OPTION_NUMBERS, true)
              ->setOptionValue(ComputerPasswordGenerator::OPTION_SYMBOLS, false)
            ;

            $password = $generator->generatePassword();
        }

        $registerUserCommand = new DomainCommand($username, $password);

        $commandGateway = $this->getContainer()->get('app.command.gateway');
        $commandGateway->send($registerUserCommand);

        $output->writeln('<info>User created!</info>');
    }
}
