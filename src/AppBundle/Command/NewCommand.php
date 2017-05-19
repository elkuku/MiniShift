<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;

class NewCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('new')
            ->setDescription('Create a new project.')
            ->addArgument('project', InputArgument::REQUIRED, 'The project name')
            ->addArgument('preserve', InputArgument::OPTIONAL, 'Preserve permissions');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $project = $input->getArgument('project');

        $fs = new Filesystem();
        $io = new SymfonyStyle($input, $output);

        $root = realpath($this->getContainer()->get('kernel')->getRootDir().'/..');
        $repoDir = $root.'/'.$this->getContainer()->getParameter('repoDir').'/'.$project.'.git';
        $workDir = $root.'/'.$this->getContainer()->getParameter('workDir');
        $webDir = $root.'/'.$this->getContainer()->getParameter('webDir');
        $gitUser = $this->getContainer()->getParameter('gitUser');

        if ($fs->exists($repoDir)) {
            throw new \UnexpectedValueException('Project exists.');
        }

        $io->title('Creating project '.$project);

        $io->section('Setting up repo dir');

        $fs->mkdir($repoDir);
        echo shell_exec('cd "'.$repoDir.'"; git --bare init 2>&1');

        $fs->copy($root.'/tpl/hooks/pre-receive', $repoDir.'/hooks/pre-receive');
        $fs->copy($root.'/tpl/hooks/post-receive', $repoDir.'/hooks/post-receive');

        $io->section('Creating working copy');

        echo shell_exec('cd "'.$workDir.'"; git clone '.$repoDir.' 2>&1');

        if (!$input->getArgument('preserve')) {
            $fs->chown($repoDir, $gitUser, true);
            $fs->chgrp($repoDir, $gitUser, true);
            $fs->chown($workDir.'/'.$project, $gitUser, true);
            $fs->chgrp($workDir.'/'.$project, $gitUser, true);
        }

        $io->section('Creating symlink in web dir');
        $fs->symlink($workDir.'/'.$project, $webDir.'/'.$project);

        $host = exec('hostname');
        $ip   = exec('hostname -I');

        $table = new Table($output);
        $table
            ->setHeaders(['', 'Host', 'Ip'])
            ->setRows([
                    ['<info>Access</info>', "http://$host/$project", "http://$ip/$project",],
                    ['<info>Clone</info>', "$gitUser@$host:$repoDir", "$gitUser@$ip:$repoDir"],
                ]);

        $io->success('Project has been created.');

        $table->render();
    }
}