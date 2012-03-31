<?php

namespace Siriux\GazerBundle\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @Route("/admin") 
 */
class DefaultController extends Controller
{
    /**
     * @Route("/", name="admin_root")
     */
    public function indexAction()
    {
        return $this->redirect($this->generateUrl('admin_dashboard'));
    }
    
    /**
     * @Route("/dashboard", name="admin_dashboard")
     * @Template
     */
    public function dashboardAction() {
        return array();
    }
}
