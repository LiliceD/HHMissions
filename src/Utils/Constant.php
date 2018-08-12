<?php

namespace App\Utils;


class Constant
{
    // User categories
    const CAT_ADMIN = 'Admin';
    const CAT_GLA = 'GLA';
    const CAT_VOLUNTEER = 'Bénévole';

    // User/Mission poles of activity (or divisions)
    const PA_GLA = 'Appui GLA';
    const PA_BRIC = 'Bricolage';
    const PA_ENRJ = 'Energie';

    // Mission statuses
    const STATUS_DEFAULT = 'Créée';
    const STATUS_ASSIGNED = 'Prise en charge';
    const STATUS_FINISHED = 'Terminée';
    const STATUS_CLOSED = 'Fermée';

    // Types of owner for an address
    const OWNER_FFH_BUILDING = "FHH - Immeuble";
    const OWNER_FFH_DIFFUSE = "FHH - Logement isolé";
    const OWNER_PRIVATE_BUILDING = "Privé - Immeuble";
    const OWNER_PRIVATE_DIFFUSE = "Privé - Logement isolé";

    // Address of file "06 DOSSIER IMMEUBLES" on Sharepoint (SP)
    const ADDRESS_SHAREPOINT_FOLDER = "https://habitatethumanisme.sharepoint.com/"
    ."sites/homehh-associations/hh69/Commun/POLE%20LOGEMENT%20INDIVIDUEL/BEN%20GLA/06%20DOSSIER%20%20IMMEUBLES";

    /**
     * Callback for User->$category
     *
     * @return array
     */
    public static function getUserCategories()
    {
        return [
            self::CAT_VOLUNTEER,
            self::CAT_GLA,
            self::CAT_ADMIN,
        ];
    }

    /**
     * Category dropdown in UserType
     *
     * @return array
     */
    public static function getUserCategoriesDropdown()
    {
        return [
            self::CAT_VOLUNTEER => 'ROLE_VOLUNTEER',
            self::CAT_GLA => 'ROLE_GLA',
            self::CAT_ADMIN => 'ROLE_ADMIN'
        ];
    }

    /**
     * Callback for Mission->$activity
     *              User->$activities
     *
     * @return array
     */
    public static function getActivities()
    {
        return [
            self::PA_GLA => self::PA_GLA,
            self::PA_BRIC => self::PA_BRIC,
            self::PA_ENRJ => self::PA_ENRJ,
        ];
    }

    /**
     * Callback for Mission->$status
     *
     * @return array
     */
    public static function getStatuses()
    {
        return array(
            // Default status when mission is created (cf Mission::__construct())
            self::STATUS_DEFAULT => self::STATUS_DEFAULT,
            // When a volunteer got assigned to the mission (cf dateAssigned)
            self::STATUS_ASSIGNED => self::STATUS_ASSIGNED,
            // When the mission has been done (cf dateFinished, conclusions)
            self::STATUS_FINISHED => self::STATUS_FINISHED,
            // When Admin reviewed conclusions and officially closed mission
            self::STATUS_CLOSED => self::STATUS_CLOSED,
        );
    }

    /**
     * Callback for Address->$ownerType
     *
     * @return array
     */
    public static function getOwnerTypes()
    {
        return array(
            self::OWNER_FFH_BUILDING => self::OWNER_FFH_BUILDING,
            self::OWNER_FFH_DIFFUSE => self::OWNER_FFH_DIFFUSE,
            self::OWNER_PRIVATE_BUILDING => self::OWNER_PRIVATE_BUILDING,
            self::OWNER_PRIVATE_DIFFUSE => self::OWNER_PRIVATE_DIFFUSE
        );
    }
}
