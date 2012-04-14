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
 * Photo management for a user (frontend)
 *
 * @Route("/photo")
 */
class DefaultController extends Controller
{
    /**
     * Lists all of the user's photos
     *
     * @Route("/list")
     * @Template()
     */
    public function indexAction()
    {
        // we should get only photos owned by the current user
        $user = $this->get('security.context')->getToken()->getUser();
        $images = $this->getImageManager()->findOwnedBy($user);

        $form = $this->createForm(new ImageType(), new Image());

        return array(
            'form' => $form->createView(),
            'images' => $images,
        );
    }

    /**
     * Views a photo
     *
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

        // the reference format has the original image dimensions
        if ($format == 'reference' && $image->getMedia()->getWidth() > 940) {
            $format = 'default_max';
        }

        // check current user to allow an administrator
        // to view a photo through this user's page (for twig exceptions)
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
     * Allows downloading an image for a given format
     *
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
     * Uploads a new photo/image
     *
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
     * Updates a photo's information
     *
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

        // to save on form space, with allow deleting from the update form
        $imagePost = $this->getRequest()->get('siriux_image');
        if (isset($imagePost['delete'])) {
            $this->getImageManager()->delete($image);
            $this->flash("Image with id $id deleted successfully.");

            return $this->redirect($this->generateUrl('home'));
        }

        // from here on it's an update
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

    /**
     * Sets a flash message
     *
     * @param string $msg
     */
    private function flash($msg)
    {
        $this->get('session')->setFlash('user', $msg);
    }

    /**
     * Returns an Image from a Media id
     *
     * @param int $media_id
     * @return Image
     * @throws NotFoundHttpException if image was not found
     * @throws AccessDeniedHttpException if user is denied access to image
     */
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

    /**
     * Returns the image manager
     *
     * @return ImageManager
     */
    private function getImageManager()
    {
        return $this->get('siriux.image.manager');
    }

    /**
     * Test access to an image using the configured download security strategy
     *
     * @param Media $media
     * @return bool
     */
    private function isGranted(Media $media)
    {
        return $this->get('sonata.media.pool')
                    ->getDownloadSecurity($media)
                    ->isGranted($media, $this->getRequest());
    }
}
