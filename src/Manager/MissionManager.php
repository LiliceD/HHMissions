<?php

namespace App\Manager;

use App\Entity\Mission;
use App\Utils\Constant;

class MissionManager
{
    /**
     * Set a given Mission's status based on its dates
     *
     * @param Mission $mission
     *
     * @return Mission
     */
    public function updateStatus(Mission $mission)
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
}
