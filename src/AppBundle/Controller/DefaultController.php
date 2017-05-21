<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class DefaultController
 * @package AppBundle\Controller
 */
class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     *
     * @return Response
     */
    public function indexAction(): Response
    {
        return $this->render(
            'default/index.html.twig',
            ['projects' => $this->get('app.project_handler')->getProjects()]
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
