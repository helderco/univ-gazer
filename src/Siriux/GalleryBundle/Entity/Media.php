<?php
/**
 * This file is part of the Gazer project.
 *
 * (c) Helder Correia <helder.mc@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Siriux\GalleryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Sonata\MediaBundle\Entity\BaseMedia as BaseMedia;
use Siriux\UserBundle\Entity\User;

/**
 * @ORM\Entity
 * @ORM\Table(name="media")
 */
class Media extends BaseMedia
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length="30")
     * 
     * @Assert\NotBlank(message="You must choose a title for this image.", groups={"New", "Update"})
     * @Assert\MinLength(limit="3", message="Title too short. Minimum is 3 characters.", groups={"New", "Update"})
     * @Assert\MaxLength(limit="30", message="Title too long. Maximum is 30 characters.", groups={"New", "Update"})
     */
    protected $title;

    /**
     * @ORM\ManyToOne(targetEntity="Siriux\UserBundle\Entity\User")
     *
     * @Assert\NotBlank(message="Who are you?", groups={"New"})
     */
    protected $user;

    /**
     * @Assert\NotBlank(message="Please upload an image.", groups={"New"})
     * @Assert\Image(maxSize="6M", groups={"New"})
     */
    protected $binaryContent;

    /**
     * Get id
     *
     * @return integer $id
     */
    public function getId()
    {
        return $this->id;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setUser(User $user)
    {
        $this->user = $user;
    }

    /**
     * Returns a human readable string from the file size.
     *
     * @return string
     */
    public function getHRSize()
    {
        $sizes = array('B', 'KB', 'MB', 'GB');
        $len = $this->getSize();
        $order = 0;
        while ($len >= 1024 && $order + 1 < count($sizes)) {
            $order++;
            $len /= 1024;
        }
        return sprintf("%.2f %s", $len, $sizes[$order]);
    }
}
