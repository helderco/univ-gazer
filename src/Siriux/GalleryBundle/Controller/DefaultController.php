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
     * @Route("/list")
     * @Template()
     */
    public function indexAction()
    {
        $user = $this->get('security.context')->getToken()->getUser();
        $images = $this->getImageManager()->findOwnedBy($user);

        $form = $this->createForm(new ImageType(), new Image());

        return array(
            'form' => $form->createView(),
            'images' => $images,
        );
    }

    /**
     * @Route("/create", name="photo_create")
     * @Method("post")
     * @Template
     */
    public function createAction()
    {
        $image = $this->getImageManager()->create();
        $form = $this->createForm(new ImageType(), $image);
        $form->bindRequest($this->getRequest());

        if ($form->isValid()) {
            $this->getImageManager()->save($image);
            $this->flash('New image added successfully.');

            return $this->redirect($this->generateUrl('home'));
        }

        return array(
            'form' => $form->createView(),
        );
    }

    private function flash($msg)
    {
        $this->get('session')->setFlash('user', $msg);
    }

    private function getImageManager()
    {
        return $this->get('siriux.image.manager');
    }
}
