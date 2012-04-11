<?php

namespace Siriux\GalleryBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Doctrine\ORM\EntityRepository;

class ImageType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $enabledGalleries = function(EntityRepository $er) {
                               return $er->createQueryBuilder('g')
                                    ->where('g.enabled = 1');
        };

        $builder
            ->add('gallery', 'entity', array(
                'class' => 'SiriuxGalleryBundle:Gallery',
                'empty_value' => '-- Choose a category --',
                'query_builder' => $enabledGalleries))
            ->add('media', 'siriux_media_type')
        ;
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'Siriux\GalleryBundle\Entity\Image',
        );
    }

    public function getName()
    {
        return 'siriux_image';
    }
}
