<?php
declare(strict_types = 1);

namespace AppBundle\Command;

use AppBundle\Entity\Project;
use AppBundle\Service\ProjectHandler;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class InfoCommand
 * @package AppBundle\Command
 */
class InfoCommand extends ContainerAwareCommand
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
            ->setName('info')
            ->setDescription('List projects.')
            ->setDefinition(
                new InputDefinition(array(
                    new InputOption('format', 'f', InputOption::VALUE_OPTIONAL, 'The output format'),
                ))
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $format = $input->getOption('format');

        if (is_null($format)) {
            $this->renderTable($this->handler->getProjects(), $output);

            return;
        }

        switch ($format) {
            case 'json':
                echo json_encode($this->handler->getProjects());
                break;

            default:
                throw new \UnexpectedValueException('Unsupported format');
        }
    }

    /**
     * @param Project[]       $projects
     * @param OutputInterface $output
     *
     * @return void
     */
    private function renderTable(array $projects, OutputInterface $output)
    {
        $tableProjects = new Table($output);
        $tableProjects->setHeaders(
            [
                [new TableCell('   Projects', ['colspan' => 5])],
                ['Project', 'Work', 'Web', 'Clone Host', 'Clone IP'],
            ]
        );

        foreach ($projects as $project) {
            $tableProjects->addRow(
                [
                    $project->dir,
                    $project->hasWorkDir ? '<bg=green;fg=black> OK </>' : '<error> NO </error>',
                    $project->hasWebDir ? '<bg=green;fg=black> OK </>' : '<error> NO </error>',
                    $project->cloneHost,
                    $project->cloneIp,
                ]
            );
        }

        $tableProjects->render();
    }
}
