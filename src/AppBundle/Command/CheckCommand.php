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
 * Class RmCommand
 * @package AppBundle\Command
 */
class CheckCommand extends ContainerAwareCommand
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
            ->setName('check')
            ->setDescription('Check a project.')
            ->addArgument('signingKey', InputArgument::REQUIRED, 'The project name')
            ->addArgument('fingerPrint', InputArgument::REQUIRED, 'The project name')
            ->addArgument('project', InputArgument::REQUIRED, 'The project name')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $signingKey = $input->getArgument('signingKey');
        $fingerprint = $input->getArgument('fingerPrint');
        $project = $input->getArgument('project');

        var_dump($signingKey);
        var_dump($fingerprint);
        var_dump($project);

        return 1;
    }
}
