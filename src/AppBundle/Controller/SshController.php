<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class SshController
 * @package AppBundle\Controller
 */
class SshController extends Controller
{
    /**
     * @Route("ssh", name="ssh.index")
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @return Response
     */
    public function indexAction()
    {
        return $this->render('ssh/index.html.twig', ['keys' => $this->get('app.ssh_handler')]);
    }
}
