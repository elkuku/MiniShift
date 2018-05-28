<?php

namespace App\Command;

use App\Service\ProjectHandler;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class RmCommand
 */
class RmCommand extends ContainerAwareCommand
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
            ->setName('rm')
            ->setDescription('Delete a project.')
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

        if (false == $fs->exists($repoDir)) {
            throw new \UnexpectedValueException('Invalid project.');
        }

        $io->title('Deleting project '.$project);

        $fs->remove($repoDir);
        $fs->remove($this->handler->getWorkDir().'/'.$project);
        $fs->remove($this->handler->getWebDir().'/'.$project);

        $io->writeln('DONE');
    }
}
