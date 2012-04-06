<?php

namespace Siriux\GalleryBundle\Security;

use Sonata\MediaBundle\Model\MediaInterface;
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

        // @todo: check if the authenticated user is the owner of this media
        return true;
    }
}

