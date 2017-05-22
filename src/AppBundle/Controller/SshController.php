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

    /**
     * @Route("ssh-add-file", name="ssh.add-file")
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function addFromFileAction(Request $request): Response
    {
        $file = $request->files->get('key_file');
        $handler = $this->get('app.ssh_handler');

        if (!$file) {
            $this->addFlash('danger', 'No file received!');
        } else {
            $path = $file->getRealPath();
            if (!$path) {
                $this->addFlash('danger', 'Invalid key file!');
            } else {
                try {
                    $key = file_get_contents($path);
                    $publicKey = new PublicKey($key);
                    $handler->addKey($publicKey);
                    $handler->save();
                    $this->addFlash('success', 'The key hs been added');
                } catch (\Exception $exception) {
                    $this->addFlash('danger', $exception->getMessage());
                }
            }
        }

        return $this->render('ssh/index.html.twig', ['keys' => $handler]);
    }

    /**
     * @Route("ssh-remove/{comment}", name="ssh.remove")
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @param string $comment
     *
     * @return Response
     */
    public function removeAction(string $comment)
    {
        $handler = $this->get('app.ssh_handler');

        $found = false;

        /* @type PublicKey $key */
        foreach ($handler as $key) {
            if ($key->getComment() == $comment) {
                $handler->removeKey($key);
                $handler->save();
                $found = true;
                $this->addFlash('success', 'The key hs been removed.');
            }
        }

        if (!$found) {
            $this->addFlash('danger', 'No such key!');
        }

        return $this->render('ssh/index.html.twig', ['keys' => $handler]);
    }
}
