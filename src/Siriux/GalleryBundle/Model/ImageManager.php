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

use Symfony\Component\Security\Core\SecurityContextInterface;
use Sonata\MediaBundle\Model\MediaManagerInterface;
use Sonata\MediaBundle\Model\GalleryManagerInterface;
use Sonata\MediaBundle\Model\GalleryHasMediaInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Siriux\GalleryBundle\Entity\Image;
use Siriux\GalleryBundle\Entity\Media;
use Siriux\GalleryBundle\Entity\Gallery;
use Siriux\UserBundle\Entity\User;

class ImageManager
{
    protected $em;
    protected $security;
    protected $repository;
    protected $galleryManager;
    protected $mediaManager;

    public function __construct(
            EntityManager $em,
            SecurityContextInterface $security,
            GalleryManagerInterface $galleryManager,
            MediaManagerInterface $mediaManager)
    {
        $this->em = $em;
        $this->security = $security;
        $this->galleryManager = $galleryManager;
        $this->mediaManager = $mediaManager;
    }

    protected function getUser()
    {
        return $this->security->getToken()->getUser();
    }

    public function save(Image $image)
    {
        $this->em->persist($image);
        $this->em->flush();
    }

    protected function getRepository()
    {
        if (!$this->repository) {
            $this->repository = $this->em->getRepository('Siriux\GalleryBundle\Entity\Image');
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
        return $this->findOneBy(array('media' => $media_id));
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

    public function findOwnedBy(User $user)
    {
        $query = $this->em->createQuery("
            SELECT i, m, g
            FROM Siriux\GalleryBundle\Entity\Image i
                JOIN i.media m
                JOIN i.gallery g
                JOIN m.user u
            WHERE u = :user
                AND m.enabled = 1
                AND g.enabled = 1
            ORDER BY m.createdAt DESC
        ")->setParameter('user', $user);

        return $query->getResult();
    }

    /**
     * Deletes an image
     *
     * @param Image $image
     * @return void
     */
    public function delete(Image $image)
    {
        $this->remove($image);
        $this->em->flush();
    }

    protected function remove(Image $image)
    {
        $this->em->remove($image->getMedia());
        $this->em->remove($image);
    }

    public function batchRemove(Gallery $gallery)
    {
        $images = $this->findBy(array('gallery' => $gallery->getId()));
        foreach ($images as $image) {
            $this->remove($image);
        }
    }
}
