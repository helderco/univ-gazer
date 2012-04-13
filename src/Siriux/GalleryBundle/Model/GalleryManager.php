<?php

/**
 * This file is part of the Gazer project.
 *
 * (c) Helder Correia <helder.mc@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Siriux\GalleryBundle\Model;

use Sonata\MediaBundle\Entity\GalleryManager as BaseManager;
use Sonata\MediaBundle\Model\GalleryInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Siriux\GalleryBundle\Entity\Gallery;
use Siriux\GalleryBundle\Model\ImageManager;

class GalleryManager extends BaseManager
{
    protected $im;

    public function __construct(EntityManager $em, $class, ImageManager $im)
    {
        $this->im = $im;

        parent::__construct($em, $class);
    }

    /**
     * Creates a new empty Gallery with default values
     * 
     * @return \Siriux\GalleryBundle\Entity\Gallery
     */
    public function create()
    {
        $gallery = new Gallery();
        $gallery->setEnabled(true);
        $gallery->setContext('default');
        $gallery->setDefaultFormat('');

        return $gallery;
    }

    /**
     * Finds all galleries
     *
     * @return \ArrayCollection
     */
    public function findAll()
    {
        return $this->getRepository()->findAll();
    }

    /**
     * Finds one gallery by id
     *
     * @param int $id
     * @return Gallery
     */
    public function find($id)
    {
        return $this->getRepository()->findOneBy(array('id' => $id));
    }

    /**
     * Deletes a gallery
     *
     * @param Gallery $gallery
     * @return void
     */
    public function delete(GalleryInterface $gallery)
    {
        $this->im->batchRemove($gallery);
        parent::delete($gallery);
    }
}
