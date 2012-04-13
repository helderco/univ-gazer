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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Siriux\GalleryBundle\Entity\Gallery;

/**
 * @Route("/admin/galleries")
 */
class GalleryController extends Controller
{
    /**
     * @Route("", name="admin_galleries")
     * @Template()
     */
    public function indexAction()
    {
        $galleries = $this->getGalleryManager()->findAll();

        $delete_forms = array();

        foreach ($galleries as $gallery) {
            $delete_forms[$gallery->getId()] = $this->createDeleteForm($gallery->getId())->createView();
        }

        return array(
            'galleries' => $galleries,
            'delete_forms' => $delete_forms,
        );
    }

    /**
     * @Route("/new", name="admin_galleries_new")
     * @Template()
     */
    public function newAction(Request $request)
    {
        $gallery = $this->getGalleryManager()->create();
        $form = $this->createEditForm($gallery);

        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);

            if ($form->isValid()) {
                $this->getGalleryManager()->update($gallery);
                $this->get('session')->setFlash('success',
                        sprintf('A new gallery with id %d was created successfully', $gallery->getId()));

                return $this->redirect($this->generateUrl('admin_galleries'));
            }
        }

        return array(
            'gallery' => $gallery,
            'form' => $form->createView(),
        );
    }

    /**
     * @Route("/{id}/edit", name="admin_gallery_edit")
     * @Template()
     */
    public function editAction($id)
    {
        $gallery = $this->getGalleryManager()->find($id);

        if (!$gallery) {
            return $this->createNotFoundException("Could not find gallery with id $id.");
        }

        $name = $gallery->getName();
        $editForm = $this->createEditForm($gallery);

        if ($this->getRequest()->getMethod() == 'POST') {
            $editForm->bindRequest($this->getRequest());

            if ($editForm->isValid()) {
                $this->getGalleryManager()->update($gallery);
                $msg = "Your changes to gallery `$name` were saved!";

                if ($name != $gallery->getName()) {
                    $msg .= sprintf(" It is now named `%s`.", $gallery->getName());
                }
                $this->flash('success', $msg);

                return $this->redirect($this->generateUrl('admin_galleries'));
            }
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'gallery' => $gallery,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * @Route("/{id}/delete", name="admin_gallery_delete")
     * @Method("post")
     */
    public function deleteAction($id)
    {
        $form = $this->createDeleteForm($id);
        $form->bindRequest($this->getRequest());

        if ($form->isValid()) {
            $gallery = $this->getGalleryManager()->find($id);

            if ($gallery !== null) {
                $this->getGalleryManager()->delete($gallery);
                $this->flash('success', sprintf("Gallery `%s` was deleted.", $gallery->getName()));
            }
            else {
                $this->flash('error', "Something went wrong! Could not find gallery with id $id.");
            }
        }

        return $this->redirect($this->generateUrl('admin_galleries'));
    }

    /**
     * Sets a flash message.
     *
     * @param string $type
     * @param string $msg
     */
    private function flash($type, $msg)
    {
        $this->get('session')->setFlash($type, $msg);
    }

    /**
     * Gets the Gallery Manager.
     *
     * @return \Siriux\GalleryBundle\Model\GalleryManager
     */
    private function getGalleryManager()
    {
        return $this->get('siriux.gallery.manager');
    }

    private function createEditForm(Gallery $gallery)
    {
        return $this->createFormBuilder($gallery)
                ->add('name')
                ->add('enabled')
                ->getForm();
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
