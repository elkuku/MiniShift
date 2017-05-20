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
     * @Route("remove/{name}", name="remove")
     *
     * @param string $name
     *
     * @return Response
     */
    public function removeAction(string $name): Response
    {
        $this->addFlash('success', 'jo '.$name);

        return $this->redirectToRoute('homepage');
    }
}
