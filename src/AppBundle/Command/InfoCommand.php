<?php
declare(strict_types = 1);

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\{
    Table, TableCell, TableSeparator
};
use Symfony\Component\Console\Input\{
    InputDefinition, InputInterface, InputOption
};
use Symfony\Component\Console\Output\{
    OutputInterface
};

/**
 * Class InfoCommand
 * @package AppBundle\Command
 */
class InfoCommand extends ContainerAwareCommand
{
    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return void
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $format = $input->getOption('format');

        $projectLister = $this->getContainer()->get('app.project_lister');

        if (is_null($format)) {
            $this->renderTable($projectLister->getProjects(), $output);

            return;
        }

        switch ($format) {
            case 'json':
                echo json_encode($projectLister->getProjects());
                break;

            default:
                throw new \UnexpectedValueException('Unsupported format');
        }
    }

    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this
            ->setName('info')
            ->setDescription('List projects.')
            ->setDefinition(
                new InputDefinition(array(
                    new InputOption('format', 'f', InputOption::VALUE_OPTIONAL, 'The output format'),
                ))
            );
    }

    /**
     * @param array           $projects
     * @param OutputInterface $output
     *
     * @return void
     */
    private function renderTable(array $projects, OutputInterface $output)
    {
        $root = realpath($this->getContainer()->get('kernel')->getRootDir().'/..');
        $repoDir = $root.'/'.$this->getContainer()->getParameter('repo_dir');
        $workDir = $root.'/'.$this->getContainer()->getParameter('work_dir');
        $webDir = $root.'/'.$this->getContainer()->getParameter('web_dir');

        $tableProjects = new Table($output);
        $tableProjects->setHeaders(
            [
                [new TableCell('   Projects', ['colspan' => 5])],
                ['Repo', 'Work', 'Web', 'Clone Host', 'Clone IP'],
            ]
        );

        foreach ($projects as $project) {
            $tableProjects->addRow(
                [
                    $project->gitDir,
                    $project->hasWorkDir ? '<bg=green;fg=black> OK </>' : '<error> NO </error>',
                    $project->hasWebDir ? '<bg=green;fg=black> OK </>' : '<error> NO </error>',
                    $project->cloneHost,
                    $project->cloneIp,
                ]
            );
        }

        $tablePaths = new Table($output);
        $tablePaths->setHeaders(
            [
                [new TableCell('   Paths', ['colspan' => 2])],
            ]
        );

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
