<?php

namespace Siriux\GazerBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class UserType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('username')
            ->add('password', 'password')
            ->add('email')
            ->add('enabled')
        ;
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'Siriux\GazerBundle\Entity\User'
        );
    }

    public function getName()
    {
        return 'siriux_user';
    }
}
