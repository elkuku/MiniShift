<?php

namespace App\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class InitDbCommand
 */
class InitDbCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('initdb')
            ->setDescription('Set up the database');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $dbPath = $this->getContainer()->getParameter('database_path');

        if (file_exists($dbPath)) {
            $io->text('Database exists');

            return;
        }

        $io->text('Creating the database...');

        $io->text($this->runCommand(['command' => 'doctrine:database:create']));
        $io->text($this->runCommand(['command' => 'doctrine:schema:update', '--force' => true]));
        $io->text($this->runCommand(['command' => 'doctrine:fixtures:load', '--append' => true]));
    }

    private function runCommand(array $command, InputDefinition $definition = null)
    {
        $application = new Application($this->getContainer()->get('kernel'));
        $application->setAutoExit(false);

        $output = new BufferedOutput();

        $application->run(new ArrayInput($command, $definition), $output);

        return $output->fetch();
    }
}
