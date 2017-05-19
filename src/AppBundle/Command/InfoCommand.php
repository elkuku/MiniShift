<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class InfoCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('info')
            ->setDescription('List projects.');

    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $fs = new Filesystem();

        $root = realpath($this->getContainer()->get('kernel')->getRootDir().'/..');
        $repoDir = $root.'/'.$this->getContainer()->getParameter('repoDir');
        $workDir = $root.'/'.$this->getContainer()->getParameter('workDir');
        $webDir = $root.'/'.$this->getContainer()->getParameter('webDir');
        $gitUser = $this->getContainer()->getParameter('gitUser');

        $tablePaths = new Table($output);
        $tablePaths->setHeaders(
            [
                [new TableCell('   Paths', ['colspan' => 2])],
            ]
        );

        $tableProjects = new Table($output);
        $tableProjects->setHeaders(
            [
                [new TableCell('   Projects', ['colspan' => 5])],
                ['Repo', 'Work', 'Web', 'Clone Host', 'Clone IP'],
            ]
        );

        $host = exec('hostname');
        $ip   = exec('hostname -I');

        $directories = glob($repoDir.'/*', GLOB_ONLYDIR);
        $projects    = [];

        foreach ($directories as $directory) {
            $projects[] = substr($fs->makePathRelative($directory, $repoDir), 0, -5);
        }

        foreach ($projects as $project) {
            $tableProjects->addRow(
                [
                    $project.'.git',
                    $fs->exists($workDir.'/'.$project) ? '<bg=green;fg=black> OK </>' : '<error> NO </error>',
                    $fs->exists($webDir.'/'.$project) ? '<bg=green;fg=black> OK </>' : '<error> NO </error>',
                    "$gitUser@$host:$repoDir/$project.git",
                    "$gitUser@$ip:$repoDir/$project.git",
                ]
            );
        }

        $tablePaths->addRows(
            [
                ['<info>Root</info>', $root],
                new TableSeparator(),
                ['<info>Repo</info>', $repoDir],
                ['<info>Work</info>', $workDir],
                ['<info>Web</info>', $webDir],
            ]
        );

        $tableProjects->render();
        $tablePaths->render();
    }
}
