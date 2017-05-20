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
    public function indexAction(Request $request):  \Symfony\Component\HttpFoundation\Response
    {
        $projects = $this->get('app.project_lister')->getProjects();

        $webDir = realpath($this->getParameter('kernel.root_dir').'/../web');
        $directories = glob($webDir.'/*', GLOB_ONLYDIR);
        $projects2 = [];

        foreach ($directories as $directory) {
            $dir =  str_replace($webDir.'/', '', $directory);
            if ('admin' == $dir) {
                continue;
            }
            $projects2[] = $dir;
        }

        return $this->render('default/index.html.twig', [
            'projects2' => $projects2,
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
