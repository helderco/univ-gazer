<?php

namespace Siriux\GazerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Siriux\GalleryBundle\Entity\Image;
use Siriux\GalleryBundle\Form\Type\ImageFormType;

class DefaultController extends Controller
{
    /**
     * @Route("", name="root")
     * @Template
     */
    public function indexAction()
    {
        if ($this->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl("home"));
        }
        // go to login if not authenticated
        return $this->redirect($this->generateUrl("fos_user_security_login"));
    }

    /**
     * @Route("/home", name="home")
     * @Template
     */
    public function homeAction()
    {
        return $this->forward('SiriuxGalleryBundle:Default:new');
    }
}
