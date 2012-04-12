<?php

namespace Siriux\GalleryBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Sonata\MediaBundle\Form\DataTransformer\ProviderDataTransformer;
use Sonata\AdminBundle\Form\DataTransformer\ArrayToModelTransformer;
use Sonata\MediaBundle\Provider\Pool;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Siriux\UserBundle\Entity\User;
use Siriux\GalleryBundle\Form\EventListener\AddUserFieldSubscriber;

class MediaType extends AbstractType
{
    protected $pool;
    protected $user;

    public function __construct(Pool $pool, SecurityContextInterface $security)
    {
        $this->pool = $pool;
        $this->user = $security->getToken()->getUser();
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
            ->add('description', 'textarea', array('required' => false))
        ;

        if (in_array('New', $options['validation_groups'])) {
            $builder
                ->add('binaryContent', 'file')
                ->addEventSubscriber(new AddUserFieldSubscriber($this->user))
            ;
        }
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'Siriux\GalleryBundle\Entity\Media',
            'provider' => null,
            'context' => null,
            'validation_groups' => array('Update'),
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
