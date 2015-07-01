<?php

namespace AppBundle\Command;

use AppBundle\Persistence\Doctrine\EventStore\DoctrineEventStore;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class InstallCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:install')
            ->setDescription('Install default database structure')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $eventStore = new DoctrineEventStore($this->getContainer()->get('database_connection'));
        $eventStore->createTable();

        $output->writeln('<info>Database updated!</info>');
    }
}
