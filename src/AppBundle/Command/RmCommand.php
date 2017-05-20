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
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $project = $input->getArgument('project');

        $fs = new Filesystem();
        $io = new SymfonyStyle($input, $output);

        $root = realpath($this->getContainer()->get('kernel')->getRootDir().'/..');
        $repoDir = $root.'/'.$this->getContainer()->getParameter('repo_dir').'/'.$project.'.git';
        $workDir = $root.'/'.$this->getContainer()->getParameter('work_dir');
        $webDir = $root.'/'.$this->getContainer()->getParameter('web_dir');

        if (false == $fs->exists($repoDir)) {
            throw new \UnexpectedValueException('Invalid project.');
        }

        $io->title('Deleting project '.$project);

        $fs->remove($repoDir);
        $fs->remove($workDir.'/'.$project);
        $fs->remove($webDir.'/'.$project);

        $io->writeln('DONE');
    }

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
}
