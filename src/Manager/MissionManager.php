<?php

namespace App\Manager;

use App\Entity\Mission;
use App\Utils\Constant;

/**
 * Class MissionManager
 *
 * @author Alice Dahan <lilice.dhn@gmail.com>
 */
class MissionManager
{
    /**
     * Set a given Mission's status based on its dates
     *
     * @param Mission $mission
     *
     * @return Mission
     */
    public static function updateStatus(Mission $mission)
    {
        if ($mission->getDateFinished()) {
            $mission->setStatus(Constant::STATUS_FINISHED);
        } elseif ($mission->getDateAssigned()) {
            $mission->setStatus(Constant::STATUS_ASSIGNED);
        } else {
            $mission->setStatus(Constant::STATUS_DEFAULT);
        }

        return $mission;
    }

    /**
     * Check whether a mission is assigned
     *
     * @param Mission $mission
     *
     * @return bool
     */
    public static function isAssigned(Mission $mission): bool
    {
        return $mission->getStatus() !== Constant::STATUS_DEFAULT;
    }

    /**
     * Check whether a mission is closed
     *
     * @param Mission $mission
     *
     * @return bool
     */
    public static function isClosed(Mission $mission): bool
    {
        return $mission->getStatus() === Constant::STATUS_CLOSED;
    }
}
