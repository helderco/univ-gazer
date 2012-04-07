<?php

namespace Siriux\GazerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="root")
     * @Template
     */
    public function indexAction()
    {
        if ($this->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl("home"));
        }
        return $this->redirect($this->generateUrl("fos_user_security_login"));
    }

    /**
     * @Route("/home", name="home")
     * @Template
     */
    public function homeAction()
    {
        return array();
    }
}
