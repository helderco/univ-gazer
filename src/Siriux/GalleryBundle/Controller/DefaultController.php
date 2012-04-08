<?php

namespace Siriux\GalleryBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Siriux\GalleryBundle\Entity\Media;
use Siriux\GalleryBundle\Entity\Gallery;
use Siriux\GalleryBundle\Entity\Image;
use Siriux\GalleryBundle\Form\Type\ImageType;

/**
 * @Route("/photo")
 */
class DefaultController extends Controller
{
    /**
     * @Route("/new", name="photo_new")
     * @Template
     */
    public function newAction()
    {
        $form = $this->createForm(new ImageType(), new Image());

        return array(
            'form' => $form->createView(),
        );
    }

    /**
     * @Route("/create", name="photo_create")
     * @Method("post")
     * @Template
     */
    public function createAction()
    {
        $form = $this->createForm(new ImageType(), new Image());
        $form->bindRequest($this->getRequest());

        $image = $form->getData();
        $this->getImageManager()->save($image);

        return $this->redirect($this->generateUrl("home"));
    }

    public function getImageManager()
    {
        return $this->get('siriux.image.manager');
    }
}
