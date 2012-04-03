<?php

namespace Siriux\UserBundle\Entity;

use FOS\UserBundle\Entity\User as AbstractUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use FOS\UserBundle\Validator\Unique as AssertUnique;

/**
 * @ORM\Entity
 * @ORM\Table(name="users")
 *
 * @AssertUnique(property="usernameCanonical", message="The chosen username is already taken.", groups={"Admin"})
 * @AssertUnique(property="emailCanonical", message="The email is already associated with another user.", groups={"Admin"})
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
     * @Assert\NotBlank(message="Please enter your name.", groups={"Registration", "Profile", "Admin"})
     * @Assert\MinLength(limit="3", message="The name is too short. Minimum is 3 characters.", groups={"Registration", "Profile", "Admin"})
     * @Assert\MaxLength(limit="254", message="The name is too long.", groups={"Registration", "Profile", "Admin"})
     */
    protected $name;

    /**
     * @Assert\NotBlank(message="Please choose a unique username.", groups={"Admin"})
     * @Assert\MinLength(limit="3", message="Username too short. Minimum is 3 characters.", groups={"Admin"})
     * @Assert\MaxLength(limit="255", message="fos_user.username.long", groups={"Admin"})
     */
    protected $username;

    /**
     * @Assert\NotBlank(message="An email address is mandatory.", groups={"Admin"})
     * @Assert\MaxLength(limit="254", message="fos_user.email.long", groups={"Admin"})
     * @Assert\Email(message="This email is not valid.", groups={"Admin"})
     */
    protected $email;

    /**
     * @Assert\MinLength(limit="6", message="The password is too short. Minimum is 6 characters.", groups={"Admin"})
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
