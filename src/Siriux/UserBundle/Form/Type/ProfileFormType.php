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

use Symfony\Component\Form\FormBuilder;
use FOS\UserBundle\Form\Type\ProfileFormType as BaseType;

/**
 * Form type to represent a user (profile)
 */
class ProfileFormType extends BaseType
{
    protected function buildUserForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('username')
            ->add('email', 'email')
        ;
    }

    public function getName()
    {
        return 'siriux_user_profile';
    }
}
