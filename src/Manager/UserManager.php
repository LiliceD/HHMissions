<?php

namespace App\Manager;

use App\Entity\User;
use App\Utils\Constant;

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
        $rolesByCategories = Constant::getRolesFromCategories();

        $user->setRoles($rolesByCategories[$category]);

        return $user;
    }
}
