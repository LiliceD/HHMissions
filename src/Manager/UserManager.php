<?php

namespace App\Manager;

use App\Entity\User;

/**
 * Class UserManager
 *
 * @author Alice Dahan <lilice.dhn@gmail.com>
 */
class UserManager
{
    /**
     * @param User   $user
     * @param string $category
     *
     * @return User $user
     */
    public function setRolesFromCategory(User $user, string $category): User
    {
        $rolesByCategories = User::getRolesFromCategories();

        $user->setRoles($rolesByCategories[$category]);

        return $user;
    }
}
