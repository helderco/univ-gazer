<?php

/**
 * This file is part of the Siriux package.
 *
 * (c) Helder Correia <helder.mc@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Siriux\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

/**
 * Form type to edit a user in the backend
 */
class UserType extends AbstractType
{
    private $validationGroup;
    private $currentUser;

    public function __construct($validationGroup, $currentUser = false)
    {
        $this->validationGroup = $validationGroup;
        $this->currentUser = $currentUser;
    }

    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('username')
            ->add('plainpassword', 'password', array('label' => 'Password'))
            ->add('email')
        ;

        // don't allow the admin to demote or disable himself
        if (!$this->currentUser) {
            $builder
                ->add('admin', 'checkbox', array('label' => 'Administrator'))
                ->add('enabled')
            ;
        }
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'Siriux\UserBundle\Entity\User',
            'validation_groups' => array($this->validationGroup),
        );
    }

    public function getName()
    {
        return 'siriux_user';
    }
}
