<?php

namespace Siriux\GalleryBundle\Model;

use Sonata\MediaBundle\Entity\GalleryManager as BaseManager;
use Siriux\GalleryBundle\Entity\Gallery;

class GalleryManager extends BaseManager
{
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
}
