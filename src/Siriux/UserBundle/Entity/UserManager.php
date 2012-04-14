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

use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Entity\UserManager as BaseUserManager;

class UserManager extends BaseUserManager
{
    /**
     * Update a user.
     *
     * When creating a user using the console, the added `name` property
     * is not accounted for and the database fails with a fatal error.
     *
     * To allow on a first configuration to add a super administrator,
     * this override provides a default name to prevent the database insert
     * from failing with a null value.
     *
     * @param UserInterface $user
     * @param bool $andFlush
     */
    public function updateUser(UserInterface $user, $andFlush = true)
    {
        if (!$user->getName() && $user->isSuperAdmin()) {
            $user->setName('Super Admin');
        }
        parent::updateUser($user, $andFlush);
    }
}
