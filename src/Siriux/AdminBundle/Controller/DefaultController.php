<?php

namespace Siriux\AdminBundle\Controller;

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
    public function dashboardAction()
    {
        return array();
    }

    /**
     * @Route("/profile", name="admin_user_profile")
     */
    public function profileAction()
    {
        $user = $this->get('security.context')->getToken()->getUser();
        return $this->forward('SiriuxUserBundle:Admin:edit', array('id' => $user->getId(), 'profile' => true));
    }
}
