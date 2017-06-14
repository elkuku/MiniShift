<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Project;
use AppBundle\Entity\User;
use AppBundle\Form\ProjectType;
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
 * Class ProjectController
 * @package AppBundle\Controller
 */
class ProjectController extends Controller
{
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
        $projectName = $request->request->get('project');
        $output      = '';
        $em          = $this->getDoctrine()->getManager();

        if (!$projectName) {
            $this->addFlash('danger', 'No project supplied');
        } else {
            try {
                $output = $this->runCommand(['command' => 'new', 'project' => $projectName]);

                $project = new Project();
                $project->setName($projectName);

                // Save
                $em->persist($project);
                $em->flush();

                $this->addFlash('success', sprintf('Project %s has been created.', $projectName));
            } catch (\Exception $exception) {
                $this->addFlash('danger', $exception->getMessage());
            }
        }

        return $this->render(
            'default/index.html.twig',
            [
               // 'projects'      => $handler->getProjects(),
                'projects'    => $this->getDoctrine()->getRepository(Project::class)->findAll(),
                'consoleOutput' => $output,
            ]
        );
    }

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
     * @Route("edit-project/{project}", name="project.edit")
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @param Project $project
     *
     * @param Request $request
     *
     * @return Response
     */
    public function editAction(Project $project, Request $request): Response
    {
        $form = $this->createForm(ProjectType::class, $project);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $project = $form->getData();
            $em = $this->getDoctrine()->getManager();

            //@todo remove users
            $users = $this->getDoctrine()
                ->getRepository(User::class)
                ->findAll();

            foreach ($users as $user) {
                if ($user->hasProject($project) && !$project->hasUser($user)) {
                    $user->removeProject($project);
                }
            }

            foreach ($project->getUsers() as $user) {
                $user->addProject($project);
                $em->persist($user);
            }

            $em->persist($project);
            $em->flush();

            $this->addFlash('success', 'User keys have been saved.');



            return $this->redirectToRoute('homepage');
        }

        return $this->render(
            'project/form.html.twig',
            [
                'form' => $form->createView(),
                'data' => $project,
                'project' => $project,
            ]
        );
    }

    /**
     * @param array $command
     *
     * @return string
     * @throws \Exception
     */
    private function runCommand(array $command): string
    {
        $bufferedOutput = new BufferedOutput();

        $application = new Application($this->get('kernel'));
        $application->setAutoExit(false);

        $exitCode = $application->run(new ArrayInput($command), $bufferedOutput);

        $output = $bufferedOutput->fetch();

        if (0 != $exitCode) {
            throw new \Exception($output);
        }

        return $output;
    }
}
