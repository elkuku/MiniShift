<?php

namespace AppBundle\Controller;

use Pagemachine\AuthorizedKeys\AuthorizedKeys;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
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
     *
     * @return Response
     */
    public function indexAction()
    {
        $key = 'ssh-rsa AAAAB3NzaC1yc2EAAAADAQABAAACAQCzW1oPysI0KmIDmO7it61AOB3TUcdqh3teM3P6mws65n918dJ50AKyB15+aIqcnGuW'
        .'EtmRGmfzT5rtu09g0mgvr+MoV5OHNCBw20wu3o2Lh1XJI/9INLPsIZW+qCq6F0wCINbT+GM+xHQC0cPWVFZgAw5/yHWNkbPS3UWkc6eopR/Ram'
        .'47hXsX3uRgDoYJhNPHdOjx6U0ixGeFqgu9REbIddxuBcgHkujcQy2sgtNBNFxRBQ6sbVMYuFmNSCbmJegnP9TcJ/k5zTtPdnOmFiFGkxuiKnSk'
        .'U3FTvL76C68WzjPZsWMGa/5wXOksN930xi7dYh89B03sZMIPg0vTU33B4H7JarqXuZrRliootGVmQ7R1vlnpoMATHv9AmaVnsEN5hBP7YKUJro'
        .'SxRFYC0pIFhsenkYw/xyvGNLk+YokULJvO/sZr1Y7qiJhQ76qQkZzrtOJ7w+x7AWCiz3PXTOaNFU2R3Q39rRlIrPW8cDifuXSOqejdYn2TvzcF'
        .'iu3Vx4eYwzo8QqUZNaUrrIA8IJ5xaRa3C8LH51ap1Rr+2irLBuorqtQMI8Gk579r99ombp7+AW/BIIiNiLW4o7Ywpvh5kxeHyBfMqtVVtyZcsv'
        .'pPjdJtbmVHapCxEdJbh4p3sy8PlDjlDhqSqQKANqyGlUeggxNQ3w1zXXj842VwwweeqQ== der.el.kuku@gmail.com';

        $authorizedKeys = new AuthorizedKeys();

        foreach ($authorizedKeys as $key) {
            // Do something with $key
            $a = $key;
        }

        return $this->render('ssh/index.html.twig', ['keys' => $authorizedKeys]);
    }
}
