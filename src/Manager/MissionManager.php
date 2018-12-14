<?php

namespace App\Manager;

use App\Entity\Mission;
use App\Repository\MissionRepository;
use App\Utils\Constant;

/**
 * Class MissionManager
 *
 * @author Alice Dahan <lilice.dhn@gmail.com>
 */
class MissionManager
{
    private $repository;

    public function __construct(MissionRepository $missionRepository)
    {
        $this->repository = $missionRepository;
    }

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

    public function getById(int $id): ?Mission
    {
        /** @var Mission $mission */
        $mission = $this->repository->findOneBy(['id' => $id]);

        return $mission;
    }

    public function getFilteredMissions(array $filters): array
    {
        // Create array of filters for query
        $queryFilters = [];

        if (!empty($filters['statuses'])) {
            $statusFilter = ['field' => 'm.status', 'value' => \explode('|', $filters['statuses'])];
            $queryFilters['statuses'] = $statusFilter;
        }

        if (!empty($filters['glaIds'])) {
            $glaFilter = ['field' => 'g.id', 'value' => $filters['glaIds']];
            $queryFilters['glaIds'] = $glaFilter;
        }

        if (!empty($filters['volunteerIds'])) {
            $volunteerFilter = ['field' => 'v.id', 'value' => $filters['volunteerIds']];
            $queryFilters['volunteerIds'] = $volunteerFilter;
        }

        if (!empty($filters['addressIds'])) {
            $addressFilter = ['field' => 'a.id', 'value' => $filters['addressIds']];
            $queryFilters['addressIds'] = $addressFilter;
        }

        if (!empty($filters['descriptionSearch'])) {
            // Query will search for missions'whose description *contains* $descriptionSearch
            $descriptionFilter = ['field' => 'm.description', 'value' => '%'.$filters['descriptionSearch'].'%'];
            $queryFilters['description'] = $descriptionFilter;
        }

        if (!empty($filters['dateCreatedMin']) || !empty($filters['dateCreatedMax'])) {
            $dateCreatedFilter = [
                'field' => 'm.dateCreated',
                'value' => ['min' => $filters['dateCreatedMin'], 'max' => $filters['dateCreatedMax']],
            ];
            $queryFilters['dateCreated'] = $dateCreatedFilter;
        }

        if (!empty($filters['dateFinishedMin']) || !empty($filters['dateFinishedMax'])) {
            $dateFinishedFilter = [
                'field' => 'm.dateFinished',
                'value' => ['min' => $filters['dateFinishedMin'], 'max' => $filters['dateFinishedMax']],
            ];
            $queryFilters['dateFinished'] = $dateFinishedFilter;
        }

        return $this->repository->findByFiltersJoined($queryFilters);
    }
}
