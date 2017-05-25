<?php

namespace AppBundle\Controller;

use AppBundle\Service\ProjectHandler;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AdminController
 * @package AppBundle\Controller
 */
class AdminController extends Controller
{
    /**
     * @Route("remove/{project}", name="remove")
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @param string         $project
     * @param ProjectHandler $handler
     *
     * @return Response
     */
    public function removeAction(string $project, ProjectHandler $handler): Response
    {
        try {
            $handler->rm($project);
            $this->addFlash('success', sprintf('Project %s has been removed.', $project));
        } catch (\Exception $exception) {
            $this->addFlash('danger', $exception->getMessage());
        }

        return $this->redirectToRoute('homepage');
    }

    /**
     * @Route("new", name="new")
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @param Request        $request
     * @param ProjectHandler $handler
     *
     * @return Response
     */
    public function newAction(Request $request, ProjectHandler $handler): Response
    {
        $project = $request->request->get('project');

        if (!$project) {
            $this->addFlash('danger', 'No project supplied');
            $output = '';
        } else {
            $output = $this->runCommand(['command' => 'new', 'project' => $project]);
            $this->addFlash('success', sprintf('Project %s has been created.', $project));
        }

        return $this->render(
            'default/index.html.twig',
            [
                'projects'      => $handler->getProjects(),
                'consoleOutput' => $output,
            ]
        );
    }

    /**
     * @param array $command
     *
     * @return string
     */
    private function runCommand(array $command): string
    {
        $output = new BufferedOutput();

        $application = new Application($this->get('kernel'));
        $application->setAutoExit(false);
        $application->run(new ArrayInput($command), $output);

        return $output->fetch();
    }
}
