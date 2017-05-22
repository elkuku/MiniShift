<?php

namespace AppBundle\Controller;

use Pagemachine\AuthorizedKeys\PublicKey;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
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
    public function indexAction(): Response
    {
        return $this->render('ssh/index.html.twig', ['keys' => $this->get('app.ssh_handler')]);
    }

    /**
     * @Route("ssh-add", name="ssh.add")
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function addAction(Request $request): Response
    {
        $key     = $request->request->get('key');
        $handler = $this->get('app.ssh_handler');

        if (!$key) {
            $this->addFlash('danger', 'No key supplied');
        } else {
            try {
                $publicKey = new PublicKey($key);
                $handler->addKey($publicKey);
                $handler->save();
                $this->addFlash('success', 'The key hs been added');

            } catch (\Exception $exception) {
                $this->addFlash('danger', $exception->getMessage());
            }
        }

        return $this->render('ssh/index.html.twig', ['keys' => $handler]);
    }
}
