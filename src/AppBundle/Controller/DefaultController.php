<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class DefaultController
 * @package AppBundle\Controller
 */
class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $webDir = realpath($this->getParameter('kernel.root_dir').'/../web');
        $directories = glob($webDir.'/*', GLOB_ONLYDIR);
        $projects = [];

        foreach ($directories as $directory) {
            $projects[] = str_replace($webDir.'/', '', $directory);
        }

        $projects = array_diff($projects, $this->getParameter('sysDirs'));

        return $this->render('default/index.html.twig', [
            'projects' => $projects,
        ]);
    }

    /**
     * @Route("about", name="default.about")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function aboutAction()
    {
        return $this->render('default/about.html.twig');
    }
}
