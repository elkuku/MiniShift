<?php

namespace MiniShift\Console\Command;

use MiniShift\Console\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;

class InfoCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('info')
            ->setDescription('List projects.')
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $fs = new Filesystem();

        $tablePaths = new Table($output);
        $tablePaths->setHeaders([
            [new TableCell('   Paths', ['colspan' => 2])],
        ]);

        $tableProjects = new Table($output);
        $tableProjects->setHeaders([
            [new TableCell('   Projects', ['colspan' => 5])],
            ['Repo', 'Work', 'Web', 'Clone Host', 'Clone IP']
        ]);

        $host = exec('hostname');
        $ip   = exec('hostname -I');

        try {
            $directories = glob($this->repoDir.'/*', GLOB_ONLYDIR);
            $projects    = [];

            foreach ($directories as $directory) {
                $projects[] = substr($fs->makePathRelative($directory, $this->repoDir), 0, -5);
            }

            foreach ($projects as $project) {
                $tableProjects->addRow(
                    [
                        $project.'.git',
                        $fs->exists($this->workDir.'/'.$project) ? '<bg=green;fg=black> OK </>' : '<error> NO </error>',
                        $fs->exists($this->webDir.'/'.$project) ? '<bg=green;fg=black> OK </>' : '<error> NO </error>',
                        "{$this->config->gitUser}@$host:{$this->repoDir}/$project.git",
                        "{$this->config->gitUser}@$ip:{$this->repoDir}/$project.git",
                    ]
                );
            }

            $tablePaths->addRows([
                ['<info>Root</info>', ROOT],
                new TableSeparator(),
                ['<info>Repo</info>', $this->config->repoDir],
                ['<info>Work</info>', $this->config->workDir],
                ['<info>Web</info>', $this->config->webDir],
            ]);

            $tableProjects->render();
            $tablePaths->render();

        } catch (IOExceptionInterface $e) {
            echo $e->getMessage();
        }
    }
}
