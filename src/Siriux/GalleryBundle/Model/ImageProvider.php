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

use Sonata\MediaBundle\Model\MediaInterface;
use Sonata\MediaBundle\Provider\ImageProvider as BaseImageProvider;
use Imagine\Exception\RuntimeException;

/**
 * Override for Sonata's image provider
 *
 * The original image provider throws an exception and bind time when
 * the file isn't an image, because the Imagine library tries to open
 * an invalid file. Validation comes after binding, so we need to
 * ignore binding errors to use our validators later with $form->isvalid()
 */
class ImageProvider extends BaseImageProvider
{
    /**
     * {@inheritdoc}
     */
    protected function doTransform(MediaInterface $media)
    {
        try {
            parent::doTransform($media);
        }
        catch (RuntimeException $e) {
            // Ignore any exceptions from the Imagine library.
            // The data will be invalid, but we'll validate it afterwards
            // with $form->isValid() in the controller.
        }
    }
}
