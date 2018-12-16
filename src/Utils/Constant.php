<?php

namespace App\Utils;

/**
 * Class Constant
 *
 * @author Alice Dahan <lilice.dhn@gmail.com>
 */
class Constant
{
    // User/Mission poles of activity (or divisions)
    const ACTIVITY_GLA = 'Appui GLA';
    const ACTIVITY_BRIC = 'Bricolage';
    const ACTIVITY_ENRJ = 'Energie';

    // Address of file "06 DOSSIER IMMEUBLES" on Sharepoint (SP)
    const ADDRESS_SHAREPOINT_FOLDER = "https://habitatethumanisme.sharepoint.com/"
    ."sites/homehh-associations/hh69/Commun/POLE%20LOGEMENT%20INDIVIDUEL/BEN%20GLA/06%20DOSSIER%20%20IMMEUBLES";

    /**
     * Callback for Mission->$activity
     *              User->$activities
     *
     * @return array
     */
    public static function getActivities()
    {
        return [
            self::ACTIVITY_GLA => self::ACTIVITY_GLA,
            self::ACTIVITY_BRIC => self::ACTIVITY_BRIC,
            self::ACTIVITY_ENRJ => self::ACTIVITY_ENRJ,
        ];
    }
}
