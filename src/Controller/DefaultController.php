<?php

namespace App\Controller;

use App\Entity\Project;
use App\Service\ProjectHandler;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class DefaultController
 */
class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     *
     * @param ProjectHandler $handler
     *
     * @return Response
     */
    public function indexAction(ProjectHandler $handler): Response
    {
        return $this->render(
            'default/index.html.twig',
            [
                //'projects' => $handler->getProjects(),
             'projects'    => $this->getDoctrine()->getRepository(Project::class)->findAll(),
            ]
        );
    }

    /**
     * @Route("about", name="default.about")
     *
     * @return Response
     */
    public function aboutAction(): Response
    {
        return $this->render('default/about.html.twig');
    }
}
