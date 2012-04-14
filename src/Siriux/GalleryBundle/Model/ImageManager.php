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

/**
 * Manager to handle Image objects
 */
class ImageManager
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var SecurityContextInterface
     */
    protected $security;

    /**
     * EntityRepository
     */
    protected $repository;

    /**
     * @var GalleryManagerInterface
     */
    protected $galleryManager;

    /**
     * @var MediaManagerInterface
     */
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

    /**
     * Get current authenticated user
     *
     * @return User
     */
    protected function getUser()
    {
        return $this->security->getToken()->getUser();
    }

    /**
     * Persist image to the database
     *
     * @param Image $image
     */
    public function save(Image $image)
    {
        $this->em->persist($image);
        $this->em->flush();
    }

    /**
     * Get the entity manager repository for Image objects
     */
    protected function getRepository()
    {
        if (!$this->repository) {
            $this->repository = $this->em->getRepository('Siriux\GalleryBundle\Entity\Image');
        }

        return $this->repository;
    }

    /**
     * Create an empty image instance
     *
     * @return Image
     */
    public function create()
    {
        return new Image();
    }

    /**
     * Find an image by id
     *
     * @param int $id
     * @return Image
     */
    public function find($id)
    {
        return $this->findOneBy(array('id' => $id));
    }

    /**
     * Find images by the given criteria, order and number of elements
     *
     * @param array $criteria
     * @param array $orderBy
     * @param int $limit
     * @return array
     */
    public function findBy(array $criteria, $orderBy = array(), $limit = null)
    {
        return $this->getRepository()->findBy($criteria, $orderBy, $limit);
    }

    /**
     * Find all images
     *
     * @return array
     */
    public function findAll()
    {
        return $this->findMany(null);
    }

    /**
     * Find images up to a limit
     *
     * A limit of null means there is no limit (same as findAll)
     *
     * @param int $limit
     * @return array
     */
    public function findMany($limit)
    {
        $orderBy = array('createdAt' => 'DESC');

        return $this->findBy(array(), $orderBy, $limit);
    }

    /**
     * Find one image by the given criteria
     *
     * @param array $criteria
     * @return Image
     */
    public function findOneBy(array $criteria)
    {
        return $this->getRepository()->findOneBy($criteria);
    }

    /**
     * Find one image from a media id
     *
     * @param int $media_id
     * @return Image
     */
    public function findOneByMedia($media_id)
    {
        return $this->findOneBy(array('media' => $media_id));
    }

    /**
     * Find all enabled images owned by a user
     *
     * @param User $user
     * @return array
     */
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
     * Delete an image
     *
     * @param Image $image
     * @return void
     */
    public function delete(Image $image)
    {
        $this->remove($image);
        $this->em->flush();
    }

    /**
     * Set an image for removal in the unit of work (entity manager)
     *
     * Needs a call to flush() for the actual deletion
     *
     * @param Image $image
     */
    protected function remove(Image $image)
    {
        $this->em->remove($image->getMedia());
        $this->em->remove($image);
    }

    /**
     * Set all images associated to a gallery for removal in the unit of work
     *
     * @param Gallery $gallery
     */
    public function batchRemove(Gallery $gallery)
    {
        $images = $this->findBy(array('gallery' => $gallery->getId()));
        foreach ($images as $image) {
            $this->remove($image);
        }
    }
}
