<?php

namespace Siriux\GalleryBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Sonata\MediaBundle\Form\DataTransformer\ProviderDataTransformer;
use Sonata\AdminBundle\Form\DataTransformer\ArrayToModelTransformer;
use Sonata\MediaBundle\Provider\Pool;

class MediaType extends AbstractType
{
    protected $pool;

    public function __construct(Pool $pool)
    {
        $this->pool  = $pool;
    }

    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->appendNormTransformer(
            new ProviderDataTransformer($this->pool, array(
                'provider' => 'sonata.media.provider.image',
                'context' => 'default',
        )));

        $builder
            ->add('title')
            ->add('description', 'textarea')
            ->add('binaryContent', 'file')
        ;
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'Siriux\GalleryBundle\Entity\Media',
            'provider' => null,
            'context' => null,
        );
    }

    public function getParent(array $options)
    {
        return 'form';
    }

    public function getName()
    {
        return 'siriux_media_type';
    }
}
