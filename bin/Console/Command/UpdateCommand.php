<?php

namespace MiniShift\Console\Command;

use MiniShift\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;

class UpdateCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('update')
            ->setDescription('Update a project.')
            ->addArgument('project', InputArgument::REQUIRED, 'The project name')
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $project = $input->getArgument('project');

        $fs = new Filesystem();
        $io = new SymfonyStyle($input, $output);

        $repoDir = $this->repoDir.'/'.$project.'.git';
        $workDir = $this->workDir.'/'.$project;
        $webDir  = $this->webDir.'/'.$project;

        if (false == $fs->exists($repoDir)) {
            throw new \UnexpectedValueException('Invalid project.');
        }

        if (false == $fs->exists($repoDir.'/refs/heads/master')) {
            throw new \UnexpectedValueException('This project does not have a valid master branch.');
        }

        try {
            $io->title('Updating project '.$project);

            if ($fs->exists($this->workDir.'/'.$project)) {
                $io->text('Checkout');
                echo shell_exec("git --work-tree=$workDir --git-dir=$repoDir checkout -f");
            } else {
                $io->text('Cloning');
                echo shell_exec("cd {$this->workDir}; git clone $repoDir");
            }

            $contents = file_get_contents($repoDir.'/refs/heads/master');

            if ($contents) {
                $fs->dumpFile($workDir.'/sha.txt', $contents);
            } else {
                $io->warning('Can not get SHA value');
            }

            if ($fs->exists($workDir.'/composer.json')) {
                $io->section('Executing composer install');
                echo shell_exec(
                    "export SYMFONY_ENV=prod;"
                    ."cd $workDir;"
                    ."composer install --no-progress --no-dev --optimize-autoloader"
                );
            }

            if ($fs->exists($workDir.'/bower.json')) {
                $io->section('Executing Bower install');
                echo shell_exec("cd $workDir; bower install");
            }

            $io->section('Setup web dir');
            if ($fs->exists($webDir)) {
                $fs->remove($webDir);
            }

            if ($fs->exists($workDir.'/web')) {
                $io->text('Setting \'web\' directory as web root');
                $fs->symlink($workDir.'/web', $webDir);
            } elseif ($fs->exists($workDir.'/www')) {
                $io->text('Setting \'www\' directory as web root');
                $fs->symlink($workDir.'/www', $webDir);
            } else {
                $io->note('Setting the PROJECT ROOT as web root');
                $fs->symlink($workDir, $webDir);
            }

            $io->writeln('DONE');
        } catch (IOExceptionInterface $e) {
            echo $e->getMessage();
        }
    }
}
