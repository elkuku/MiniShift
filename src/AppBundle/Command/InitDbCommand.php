<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class InitDbCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('app:init-db')
            ->setDescription('Set up the database');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $path     = realpath($this->getContainer()->get('kernel')->getRootDir().'/../var');
        $fileName = 'database.sqlitec';

        if (realpath($path.'/'.$fileName)) {
            $io->text('Database exists');

            return;
        }

        $io->text('Creating the database...');

        $io->text($this->runCommand(['command' => 'doctrine:database:create']));
        $io->text($this->runCommand(['command' => 'doctrine:schema:update', '--force' => true]));
    }

    private function runCommand(array $command, InputDefinition $definition = null)
    {
        $kernel = $this->getContainer()->get('kernel');
        $application = new Application($kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput($command, $definition);
        $output = new BufferedOutput();

        $application->run($input, $output);

        return $output->fetch();
    }
}
