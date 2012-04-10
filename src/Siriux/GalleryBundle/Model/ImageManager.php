<?php

namespace Siriux\GalleryBundle\Model;

use Symfony\Component\Security\Core\SecurityContextInterface;
use Sonata\MediaBundle\Model\MediaManagerInterface;
use Sonata\MediaBundle\Model\GalleryManagerInterface;
use Sonata\MediaBundle\Model\GalleryHasMediaInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Siriux\GalleryBundle\Entity\Image;
use Siriux\UserBundle\Entity\User;

class ImageManager
{
    protected $em;
    protected $repository;
    protected $galleryManager;
    protected $mediaManager;

    public function __construct(
            EntityManager $em,
            GalleryManagerInterface $galleryManager,
            MediaManagerInterface $mediaManager)
    {
        $this->em = $em;
        $this->galleryManager = $galleryManager;
        $this->mediaManager = $mediaManager;
    }

    public function save(Image $image)
    {
        $this->em->persist($image);
        $this->em->flush();
    }

    protected function getRepository()
    {
        if (!$this->repository) {
            $this->repository = $this->em->getRepository(Image);
        }

        return $this->repository;
    }

    /**
     * Creates an empty image instance
     *
     * @return Image
     */
    public function create()
    {
        return new Image();
    }

    /**
     * Finds one image by the given criteria
     *
     * @param array $criteria
     * @return Image
     */
    public function findOneBy(array $criteria)
    {
        return $this->getRepository()->findOneBy($criteria);
    }

    public function findOneByMedia($media_id)
    {
        return $this->findOneBy(array('media_id' => $media_id));
    }

    /**
     * Finds images by the given criteria
     *
     * @param array $criteria
     * @return array
     */
    public function findBy(array $criteria)
    {
        return $this->getRepository()->findBy($criteria);
    }

    /**
     * Deletes an image
     *
     * @param Image $image
     * @return void
     */
    public function delete(Image $image)
    {
        $this->em->remove($image);
        $this->em->flush();
    }
}
