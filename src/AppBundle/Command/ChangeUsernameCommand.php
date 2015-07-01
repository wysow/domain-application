<?php

namespace AppBundle\Command;

use AppBundle\Identity\ChangeUsernameCommand as DomainCommand;
use Hackzilla\PasswordGenerator\Generator\ComputerPasswordGenerator;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ChangeUsernameCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:user:change-username')
            ->setDescription('Change username of a registered user')
            ->addArgument(
                'id',
                InputArgument::REQUIRED,
                'Which identifier?'
            )
            ->addArgument(
                'username',
                InputArgument::REQUIRED,
                'Which username?'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $id       = $input->getArgument('id');
        $username = $input->getArgument('username');

        $changeUsernameCommand = new DomainCommand($id, $username);

        $commandGateway = $this->getContainer()->get('app.command.gateway');
        $commandGateway->send($changeUsernameCommand);

        $output->writeln('<info>Username changed!</info>');
    }
}
