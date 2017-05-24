<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class RmCommand
 * @package AppBundle\Command
 */
class RmCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('rm')
            ->setDescription('Delete a project.')
            ->addArgument('project', InputArgument::REQUIRED, 'The project name');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $project = $input->getArgument('project');
        $handler = $this->getContainer()->get('app.project_handler');

        $fs = new Filesystem();
        $io = new SymfonyStyle($input, $output);

        $repoDir = $handler->getRepoDir().'/'.$handler->getRepoDirName($project);

        if (false == $fs->exists($repoDir)) {
            throw new \UnexpectedValueException('Invalid project.');
        }

        $io->title('Deleting project '.$project);

        $fs->remove($repoDir);
        $fs->remove($handler->getWorkDir().'/'.$project);
        $fs->remove($handler->getWebDir().'/'.$project);

        $io->writeln('DONE');
    }
}
