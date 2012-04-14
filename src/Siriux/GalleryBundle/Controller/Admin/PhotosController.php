<?php

/**
 * This file is part of the Gazer project.
 *
 * (c) Helder Correia <helder.mc@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Siriux\GalleryBundle\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use Siriux\GalleryBundle\Model\ImageManager;

/**
 * Photos management from the backend
 *
 * @Route("/admin/photos")
 */
class PhotosController extends Controller
{
    /**
     * Displays all photos
     *
     * @Route("", name="admin_photos")
     * @Template()
     */
    public function indexAction()
    {
        $images = $this->getImageManager()->findAll();

        return array(
            'images' => $images,
            'delete_forms' => $this->createDeleteForms($images),
        );
    }

    /**
     * Displays all photos in a given gallery
     *
     * @Route("/gallery/{id}",
     *        requirements={"id" = "\d+"},
     *        name="admin_gallery_photos")
     *
     * @Template()
     */
    public function galleryAction($id)
    {
        $gallery = $this->getGallery($id);
        $images = $this->getImageManager()->findBy(array('gallery' => $id));

        return array(
            'images' => $images,
            'gallery' => $gallery,
            'delete_forms' => $this->createDeleteForms($images),
        );
    }

    /**
     * Displays all photos from a given user
     *
     * @Route("/user/{id}",
     *        requirements={"id" = "\d+"},
     *        name="admin_user_photos")
     *
     * @Template()
     */
    public function userAction($id)
    {
        $user = $this->getUser($id);
        $images = $this->getImageManager()->findOwnedBy($user);

        return array(
            'images' => $images,
            'user' => $user,
            'delete_forms' => $this->createDeleteForms($images),
        );
    }

    /**
     * Deletes a photo
     *
     * @Route("/{id}/delete",
     *        requirements={"id" = "\d+"},
     *        name="admin_photo_delete")
     *
     * @Method("post")
     * @Template()
     */
    public function deleteAction($id)
    {
        $image = $this->getImage($id);
        $title = $image->getMedia()->getTitle();
        $this->getImageManager()->delete($image);

        $this->get('session')->setFlash('success', "Photo with id $id ($title) was deleted successfully.");

        return $this->redirect($this->generateUrl('admin_photos'));
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
     * Returns a delete form for an image
     *
     * @param integer $id
     * @return Form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }

    /**
     * Returns an array with delete forms from an array of Image objects
     *
     * @param array $images
     * @return array
     */
    private function createDeleteForms($images)
    {
        $delete_forms = array();

        foreach ($images as $image) {
            $delete_forms[$image->getId()] = $this->createDeleteForm($image->getId())->createView();
        }

        return $delete_forms;
    }

    /**
     * Returns a Gallery object from an id
     *
     * @param integer $id
     * @return SiriuxGalleryBundle\Entity\Gallery
     * @throws NotFoundHttpException if gallery is not found
     */
    private function getGallery($id)
    {
        $gallery = $this->get('siriux.gallery.manager')->find($id);

        if (!$gallery) {
            throw $this->createNotFoundException("Unable to find gallery with id $id.");
        }

        return $gallery;
    }

    /**
     * Returns an Image object from an id
     *
     * @param integer $id
     * @return SiriuxGalleryBundle\Entity\Image
     * @throws NotFoundHttpException if image is not found
     */
    private function getImage($id)
    {
        $image = $this->getImageManager()->findOneByMedia($id);

        if (!$image) {
            throw $this->createNotFoundException("Unable to find photo with id $id.");
        }

        return $image;
    }

    /**
     * Gets a user from an id.
     *
     * @param integer $id
     * @return SiriuxUserBundle\Entity\User
     * @throws NotFoundHttpException if user is not found.
     */
    private function getUser($id) {
        $user = $this->get('fos_user.user_manager')->findUserBy(array('id' => $id));

        if (!$user) {
            throw $this->createNotFoundException("Unable to find user with id $id.");
        }

        return $user;
    }
}
