<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AdminController extends Controller
{
    /**
     * @Route("remove", name="remove")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function removeAction($name)
    {
        var_dump($name);
        die();
        return $this->render('', array('name' => $name));
    }
}
