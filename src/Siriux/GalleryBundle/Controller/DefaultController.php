<?php

/**
 * This file is part of the Gazer project.
 *
 * (c) Helder Correia <helder.mc@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Siriux\GalleryBundle\Controller;

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
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
     * @Route("/{id}/view/{format}",
     *        requirements={"id" = "\d+"},
     *        defaults={"format" = "reference"},
     *        name="photo_show")
     *
     * @Template()
     */
    public function viewAction($id, $format)
    {
        $image = $this->getImage($id);
        $form = $this->createForm(new ImageType('Update'), $image);

        if ($format == 'reference' && $image->getMedia()->getWidth() > 940) {
            $format = 'default_max';
        }

        $user = $image->getMedia()->getUser();
        $authUser = $this->get('security.context')->getToken()->getUser();
        $currentUser = $user->isUser($authUser);

        return array(
            'form' => $form->createView(),
            'image' => $image,
            'format' => $format,
            'user' => $user,
            'is_current_user' => $currentUser,
        );
    }

    /**
     * @Route("/{id}/download/{format}",
     *          requirements={"id" = "\d+"},
     *          defaults={"format" = "reference"},
     *          name="photo_download")
     */
    public function downloadAction($id, $format = 'reference')
    {
        $image = $this->getImage($id);
        $media = $image->getMedia();

        $pool = $this->get('sonata.media.pool');
        $provider = $pool->getProvider($media->getProviderName());
        $downloadMode = $pool->getDownloadMode($media);

        return $provider->getDownloadResponse($media, $format, $downloadMode);
    }

    /**
     * @Route("/create", name="photo_create")
     * @Method("post")
     * @Template()
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

    /**
     * @Route("/{id}/update",
     *        requirements={"id" = "\d+"},
     *        name="photo_update")
     *
     * @Method("post")
     * @Template()
     */
    public function updateAction($id)
    {
        $image = $this->getImage($id);

        $imagePost = $this->getRequest()->get('siriux_image');
        if (isset($imagePost['delete'])) {
            $this->getImageManager()->delete($image);
            $this->flash("Image with id $id deleted successfully.");

            return $this->redirect($this->generateUrl('home'));
        }

        $form = $this->createForm(new ImageType('Update'), $image);
        $form->bindRequest($this->getRequest());

        if ($form->isValid()) {
            $this->getImageManager()->save($image);
            $this->flash('Image updated successfully.');

            return $this->redirect($this->generateUrl('photo_show', array('id' => $id)));
        }

        return array(
            'image' => $image,
            'form' => $form->createView(),
        );
    }

    private function flash($msg)
    {
        $this->get('session')->setFlash('user', $msg);
    }

    private function getImage($media_id)
    {
        $image = $this->getImageManager()->findOneBy(array('media' => $media_id));

        if (!$image || !$image->getMedia()) {
            throw $this->createNotFoundException("Unknown image with the id $media_id!");
        }

        if (!$this->isGranted($image->getMedia())) {
            throw new AccessDeniedHttpException();
        }

        return $image;
    }

    private function getImageManager()
    {
        return $this->get('siriux.image.manager');
    }

    private function isGranted(Media $media)
    {
        return $this->get('sonata.media.pool')
                    ->getDownloadSecurity($media)
                    ->isGranted($media, $this->getRequest());
    }
}
