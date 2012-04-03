<?php

namespace Siriux\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Siriux\UserBundle\Entity\User;
use Siriux\UserBundle\Form\Type\UserType;

/**
 * Admin panel controller.
 *
 * @Route("/admin/users")
 */
class AdminController extends Controller
{
    /**
     * Lists all User entities.
     *
     * @Route("/", name="users")
     * @Template()
     */
    public function indexAction()
    {
        $users = $this->getUserManager()->findUsers();
        
        $users_list = array();
        $admins_list = array();
        
        foreach ($users as $user) {
            if ($user->hasRole('ROLE_ADMIN') || $user->isSuperAdmin()) {
                array_push($admins_list, $user);
            } else {
                array_push($users_list, $user);
            }
        }

        return array('users' => $users_list, 'admins' => $admins_list);
    }

    /**
     * Displays a form to create a new User entity.
     *
     * @Route("/new", name="users_new")
     * @Template()
     */
    public function newAction()
    {
        $entity = new User();
        $form   = $this->createForm(new UserType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView()
        );
    }

    /**
     * Creates a new User entity.
     *
     * @Route("/create", name="users_create")
     * @Method("post")
     * @Template("SiriuxGazerBundle:User:new.html.twig")
     */
    public function createAction()
    {
        $entity  = new User();
        $request = $this->getRequest();
        $form    = $this->createForm(new UserType(), $entity);
        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('users', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView()
        );
    }

    /**
     * Displays a form to edit an existing User entity.
     *
     * @Route("/{id}/edit", name="users_edit")
     * @Template()
     */
    public function editAction($id, $profile = false)
    {
        $user = $this->getUser($id);
        $currentUser = $this->isCurrentUser($user);

        if ($currentUser && !$profile) {
            return $this->redirect($this->generateUrl('admin_user_profile'));
        }

        $editForm = $this->createForm(new UserType($currentUser), $user);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'user'          => $user,
            'current_user'  => $currentUser,
            'edit_form'     => $editForm->createView(),
            'delete_form'   => $deleteForm->createView(),
        );
    }

    /**
     * Updates an existing User entity.
     *
     * @Route("/{id}/update", name="users_update")
     * @Method("post")
     * @Template("SiriuxUserBundle:Admin:edit.html.twig")
     */
    public function updateAction($id)
    {
        $user = $this->getUser($id);
        $currentUser = $this->isCurrentUser($user);

        $editForm = $this->createForm(new UserType($currentUser), $user);

        $request = $this->get('request');
        $editForm->bindRequest($request);

        if ($editForm->isValid()) {
            $this->getUserManager()->updateUser($user);
            $this->get('session')->setFlash('success', 'Your changes were saved!');

            return $this->redirect($this->generateUrl('users_edit', array('id' => $id)));
        } 

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'user'          => $user,
            'current_user'  => $currentUser,
            'edit_form'     => $editForm->createView(),
            'delete_form'   => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a User entity.
     *
     * @Route("/{id}/delete", name="users_delete")
     * @Method("post")
     */
    public function deleteAction($id)
    {
        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

        $form->bindRequest($request);

        if ($form->isValid()) {
            $user = $this->getUser($id);

            if ($this->isCurrentUser($user)) {
                throw new AccessDeniedHttpException("You can't delete yourself!");
            }

            $this->getUserManager()->deleteUser($user);
        }

        return $this->redirect($this->generateUrl('users'));
    }

    /**
     * Gets FOSUserBundle's user manager.
     *
     * @return FOS\UserBundle\Model\UserManager
     */
    private function getUserManager()
    {
        return $this->get('fos_user.user_manager');
    }

    /**
     * Gets a user from an id.
     *
     * @param int $id
     * @return SiriuxUserBundle\Entity\User
     * @throws NotFoundHttpException if user is not found.
     */
    private function getUser($id) {
        $user = $this->getUserManager()->findUserBy(array('id' => $id));

        if (!$user) {
            throw $this->createNotFoundException("Unable to find user with id $id.");
        }

        return $user;
    }

    /**
     * Tests weather a certain user is the one currently authenticated.
     *
     * @param SiriuxUserBundle\Entity\User $user
     * @return boolean
     */
    private function isCurrentUser($user)
    {
        $securityToken = $this->get('security.context')->getToken();
        return $user->isUser($securityToken->getUser());
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
