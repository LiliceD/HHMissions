<?php

namespace App\Manager;

use App\Entity\User;

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
        $roles = explode(',', $category);

        $user->setRoles($roles);

        return $user;
    }
}
