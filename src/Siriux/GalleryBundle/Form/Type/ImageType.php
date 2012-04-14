<?php

/**
 * This file is part of the Gazer project.
 *
 * (c) Helder Correia <helder.mc@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Siriux\GalleryBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Doctrine\ORM\EntityRepository;

/**
 * Form type that represents an Image object
 *
 * Holds a Gallery reference and a Media reference.
 */
class ImageType extends AbstractType
{
    /**
     * Validation group
     *
     * @var string
     */
    private $validation;

    /**
     * Constructor
     *
     * @param string $validation
     */
    public function __construct($validation = 'New')
    {
        $this->validation = $validation;
    }

    public function buildForm(FormBuilder $builder, array $options)
    {
        // show to the user only galleries that are enabled
        $enabledGalleries = function(EntityRepository $er) {
                               return $er->createQueryBuilder('g')
                                    ->where('g.enabled = 1');
        };

        $builder->add('gallery', 'entity', array(
            'class' => 'SiriuxGalleryBundle:Gallery',
            'empty_value' => '-- Choose a gallery --',
            'query_builder' => $enabledGalleries));

        $builder->add('media', 'siriux_media_type', array(
            'validation_groups' => array($this->validation)));

        // if it's not a new upload, allow delete
        if ($this->validation == 'Update') {
            $builder->add('delete', 'checkbox', array(
                'label' => 'Delete photo?',
                'property_path' => false,
            ));
        }
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
