<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AdminController
 * @package AppBundle\Controller
 */
class AdminController extends Controller
{
    /**
     * @Route("remove/{project}", name="remove")
     *
     * @param string $project
     *
     * @return Response
     */
    public function removeAction(string $project): Response
    {
        try {
            $this->get('app.project_handler')->rm($project);
            $this->addFlash('success', sprintf('Project %s has been removed.', $project));

        } catch (\Exception $exception) {
            $this->addFlash('danger', $exception->getMessage());
        }

        return $this->redirectToRoute('homepage');
    }
}
