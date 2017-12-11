<?php

namespace AppBundle\Command;

use AppBundle\Service\ProjectHandler;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class UpdateCommand
 * @package AppBundle\Command
 */
class UpdateCommand extends ContainerAwareCommand
{
    /**
     * @var ProjectHandler
     */
    private $handler;

    /**
     * InfoCommand constructor.
     *
     * @param ProjectHandler $handler
     * @param null           $name
     */
    public function __construct(ProjectHandler $handler, $name = null)
    {
        $this->handler = $handler;

        parent::__construct($name);
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('update')
            ->setDescription('Update a project.')
            ->addArgument('project', InputArgument::REQUIRED, 'The project name');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $project = $input->getArgument('project');

        $fs = new Filesystem();
        $io = new SymfonyStyle($input, $output);

        $repoDir = $this->handler->getRepoDir().'/'.$this->handler->getRepoDirName($project);
        $workDir = $this->handler->getWorkDir().'/'.$project;
        $webDir  = $this->handler->getWebDir().'/'.$project;

        if (false == $fs->exists($repoDir)) {
            throw new \UnexpectedValueException('Invalid project.');
        }

        if (false == $fs->exists($repoDir.'/refs/heads/master')) {
            throw new \UnexpectedValueException('This project does not have a valid master branch.');
        }

        $io->title('Updating project '.$project);

        if ($fs->exists($workDir)) {
            // $fs->remove($workDir);
            $io->text('Checkout');
            echo shell_exec("git --work-tree=$workDir --git-dir=$repoDir checkout -f");
            //        if ($fs->exists($workDir.'/.gitmodules')) {
            //          $io->text($workDir);
            //        echo shell_exec("cd $workDir; pwd; git submodule init");
            //        echo shell_exec("cd $workDir && pdw && git submodule update");
        } else {
            $io->text('Cloning');
            echo shell_exec("cd {$this->handler->getWorkDir()}; git clone --recursive $repoDir $project");
        }

        $contents = file_get_contents($repoDir.'/refs/heads/master');

        if ($contents) {
            $fs->dumpFile($workDir.'/sha.txt', $contents);
        } else {
            $io->warning('Can not get SHA value');
        }

        if ($fs->exists($workDir.'/composer.json')) {
            $io->section('Executing composer install');
            // @codingStandardsIgnoreStart
            // Concat operator must not be surrounded by spaces...
            echo shell_exec(
                "export SYMFONY_ENV=prod;export APP_ENV=prod;"
                ."cd $workDir;"
                ."composer install --no-progress --no-dev --optimize-autoloader"
            );
            // @codingStandardsIgnoreEnd
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
        } elseif ($fs->exists($workDir.'/public')) {
            $io->text('Setting \'public\' directory as web root');
            $fs->symlink($workDir.'/public', $webDir);
        } else {
            $io->note('Setting the PROJECT ROOT as web root');
            $fs->symlink($workDir, $webDir);
        }

        $io->writeln('DONE');
    }
}
