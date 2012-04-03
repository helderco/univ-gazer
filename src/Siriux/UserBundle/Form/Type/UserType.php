<?php

namespace Siriux\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

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
