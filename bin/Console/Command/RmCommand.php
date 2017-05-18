<?php

namespace MiniShift\Console\Command;

use MiniShift\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;

class RmCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('rm')
            ->setDescription('Delete a project.')
            ->addArgument('project', InputArgument::REQUIRED, 'The project name')
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $project = $input->getArgument('project');

        $fs = new Filesystem();
        $io = new SymfonyStyle($input, $output);

        $repoDirName = $project.'.git';

        if (false == $fs->exists($this->repoDir.'/'.$repoDirName)) {
            throw new \UnexpectedValueException('Invalid project.');
        }

        try {
            $io->title('Deleting project '.$project);

            $fs->remove($this->repoDir.'/'.$repoDirName);
            $fs->remove($this->workDir.'/'.$project);
            $fs->remove($this->webDir.'/'.$project);

            $io->writeln('DONE');
        } catch (IOExceptionInterface $e) {
            echo $e->getMessage();
        }
    }
}
