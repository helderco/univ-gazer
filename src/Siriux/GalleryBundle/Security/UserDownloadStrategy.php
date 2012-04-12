<?php

/**
 * This file is part of the Gazer project.
 *
 * (c) Helder Correia <helder.mc@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Siriux\GalleryBundle\Security;

use Sonata\MediaBundle\Model\MediaInterface;
use Sonata\MediaBundle\Security\DownloadStrategyInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContextInterface;

class UserDownloadStrategy implements DownloadStrategyInterface
{
    protected $security;

    /**
     * @param \Symfony\Component\Security\Core\SecurityContextInterface $security
     */
    public function __construct(SecurityContextInterface $security)
    {
        $this->security = $security;
    }

    public function getDescription()
    {
        return "The media can be retrieved by the user that uploaded it, or by an administrator.";
    }

    /**
     * @param \Sonata\MediaBundle\Model\MediaInterface $media
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return bool
     */
    public function isGranted(MediaInterface $media, Request $request)
    {
        if (!$this->security->getToken()) {
            return false;
        }

        if ($this->security->isGranted(array('ROLE_ADMIN'))) {
            return true;
        }

        return $this->security->getToken()->getUser()->isUser($media->getUser());
    }
}

