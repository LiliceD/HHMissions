<?php

namespace App\Manager;

use App\Entity\User;
use App\Utils\Constant;

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
        $rolesByCategories = Constant::getRoles();

        $user->setRoles($rolesByCategories[$category]);

        return $user;
    }
}
