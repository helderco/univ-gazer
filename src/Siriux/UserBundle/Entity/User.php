<?php

/**
 * This file is part of the Siriux package.
 *
 * (c) Helder Correia <helder.mc@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Siriux\UserBundle\Entity;

use FOS\UserBundle\Entity\User as AbstractUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use FOS\UserBundle\Validator\Unique as AssertUnique;

/**
 * @ORM\Entity
 * @ORM\Table(name="users")
 *
 * @AssertUnique(property="usernameCanonical", message="The chosen username is already taken.", groups={"AdminNew", "AdminEdit"})
 * @AssertUnique(property="emailCanonical", message="The email is already associated with another user.", groups={"AdminNew", "AdminEdit"})
 */
class User extends AbstractUser
{
    const ROLE_ADMIN = 'ROLE_ADMIN';
    
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length="255")
     *
     * @Assert\NotBlank(message="Please enter a name.", groups={"Registration", "Profile", "AdminNew", "AdminEdit"})
     * @Assert\MinLength(limit="3", message="The name is too short. Minimum is 3 characters.", groups={"Registration", "Profile", "AdminNew", "AdminEdit"})
     * @Assert\MaxLength(limit="254", message="The name is too long.", groups={"Registration", "Profile", "AdminNew", "AdminEdit"})
     */
    protected $name;

    /**
     * @Assert\NotBlank(message="Please choose a unique username.", groups={"AdminNew", "AdminEdit"})
     * @Assert\MinLength(limit="3", message="Username too short. Minimum is 3 characters.", groups={"AdminNew", "AdminEdit"})
     * @Assert\MaxLength(limit="255", message="fos_user.username.long", groups={"AdminNew", "AdminEdit"})
     */
    protected $username;

    /**
     * @Assert\NotBlank(message="An email address is mandatory.", groups={"AdminNew", "AdminEdit"})
     * @Assert\MaxLength(limit="254", message="fos_user.email.long", groups={"AdminNew", "AdminEdit"})
     * @Assert\Email(message="This email is not valid.", groups={"AdminNew", "AdminEdit"})
     */
    protected $email;

    /**
     * @Assert\NotBlank(message="fos_user.password.blank", groups={"AdminNew"})
     * @Assert\MinLength(limit="6", message="The password is too short. Minimum is 6 characters.", groups={"AdminNew", "AdminEdit"})
     */
    protected $plainPassword;

    public function __construct()
    {
        parent::__construct();
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function isAdmin()
    {
        return $this->hasRole(self::ROLE_ADMIN);
    }

    public function setAdmin($boolean)
    {
        if ($boolean) {
            $this->addRole(self::ROLE_ADMIN);
        } else {
            $this->removeRole(self::ROLE_ADMIN);
        }

        return $this;
    }
}
