<?php

namespace AppBundle\Command;

use AppBundle\Entity\Project;
use AppBundle\Service\ProjectHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

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

    private $entityManager;

    /**
     * InfoCommand constructor.
     *
     * @param ProjectHandler         $handler
     * @param EntityManagerInterface $em
     * @param null                   $name
     */
    public function __construct(ProjectHandler $handler, EntityManagerInterface $em, $name = null)
    {
        $this->handler = $handler;
        $this->entityManager = $em;

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
            ->addArgument('fingerPrint', InputArgument::REQUIRED, 'The project name')
            ->addArgument('project', InputArgument::REQUIRED, 'The project name')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $fingerprint = $input->getArgument('fingerPrint');
        $projectName = $input->getArgument('project');

        $projects = $this->entityManager->getRepository(Project::class)->findAll();

        $io = new SymfonyStyle($input, $output);

        foreach ($projects as $project) {
            if ($projectName === $project->getName()) {
                if (!count($project->getUsers())) {
                    $io->text('Project does not have access control setup.');

                    return 0;
                }

                foreach ($project->getUsers() as $user) {
                    if ($fingerprint === $user->getGpgFpr()) {
                        $io->success(sprintf('Access granted for user "%s" (%s)', $user->getUsername(), $user->getEmail()));

                        return 0;
                    }
                }

                return 1;
            }
        }

        $io->error('Project not found');

        return 1;
    }
}
