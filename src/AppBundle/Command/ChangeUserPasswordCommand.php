<?php

namespace AppBundle\Command;

use AppBundle\Identity\ChangeUserPasswordCommand as DomainCommand;
use Hackzilla\PasswordGenerator\Generator\ComputerPasswordGenerator;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ChangeUserPasswordCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:user:change-password')
            ->setDescription('Change username of a registered user')
            ->addArgument(
                'id',
                InputArgument::REQUIRED,
                'Which identifier?'
            )
            ->addArgument(
                'password',
                InputArgument::REQUIRED,
                'Which password?'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $id       = $input->getArgument('id');
        $password = $input->getArgument('password');

        $changePasswordCommand = new DomainCommand($id, $password);

        $commandGateway = $this->getContainer()->get('app.command.gateway');
        $commandGateway->send($changePasswordCommand);

        $output->writeln('<info>Password changed!</info>');
    }
}
