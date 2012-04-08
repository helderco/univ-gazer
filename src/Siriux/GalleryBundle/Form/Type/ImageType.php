<?php

namespace Siriux\GalleryBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class ImageType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('gallery', 'entity', array(
                'class' => 'SiriuxGalleryBundle:Gallery',
                'empty_value' => '-- Choose a category --'))
            ->add('media', 'siriux_media_type')
        ;
    }

    public function getName()
    {
        return 'siriux_image';
    }
}
