<?php

namespace Siriux\UserBundle\Form\Type;

use Symfony\Component\Form\FormBuilder;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;

class RegistrationFormType extends BaseType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('name');
        parent::buildForm($builder, $options);
    }

    public function getName()
    {
        return 'siriux_user_registration';
    }
}
