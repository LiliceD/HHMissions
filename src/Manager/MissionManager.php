<?php

namespace App\Manager;

use App\Entity\Mission;
use App\Entity\User;
use App\Model\FLashMessage;
use App\Repository\MissionRepository;
use App\Service\FileUploader;
use App\Utils\Constant;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\InvalidParameterException;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class MissionManager
 *
 * @author Alice Dahan <lilice.dhn@gmail.com>
 */
class MissionManager
{
    /** @var MissionRepository */
    private $repository;

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var RouterInterface */
    private $router;

    /** @var FileUploader */
    private $fileUploader;

    public function __construct(MissionRepository $missionRepository, EntityManagerInterface $entityManager, RouterInterface $router, FileUploader $fileUploader)
    {
        $this->repository = $missionRepository;
        $this->entityManager = $entityManager;
        $this->router = $router;
        $this->fileUploader = $fileUploader;
    }

    public function persist(Mission $mission, bool $flush = true): void
    {
        $this->entityManager->persist($mission);

        if ($flush) {
            $this->entityManager->flush();
        }
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

    public function create(Mission $mission): void
    {
        $file = $mission->getAttachment();
        if ($file) {
            $fileName = $this->fileUploader->upload($file);
            $mission->setAttachment($fileName);
        }

        $this->persist($mission);
    }

    public function setAttachmentAsFile(Mission $mission): Mission
    {
        $fileName = $mission->getAttachment();

        if ($fileName) {
            $mission->setAttachment(new File(sprintf('%s/%s', $this->fileUploader->getTargetDir(), $fileName)));
        }

        return $mission;
    }

    public function update(Mission $mission, Mission $oldMission, User $user)
    {
        // Activity, dateCreated, address and GLA can't be changed
        $mission->setActivity($oldMission->getActivity());
        $mission->setDateCreated($oldMission->getDateCreated());
        $mission->setAddress($oldMission->getAddress());
        $mission->setGla($oldMission->getGla());

        // DateAssigned and Volunteer can only be changed by admin
        if (!$user->isAdmin()) {
            $mission->setDateAssigned($oldMission->getDateAssigned());
            $mission->setVolunteer($oldMission->getVolunteer());
        }

        // DateFinished can only be changed by volunteer assigned or admin
        if (!(($user->isVolunteer() && $mission->getVolunteer() === $user) || $user->isAdmin())) {
            $mission->setDateFinished($oldMission->getDateFinished());
        }

        // Description and Info can only be changed by GLA or admin
        if (!$user->isAdmin()) {
            $mission->setDescription($oldMission->getDescription());
            $mission->setInfo($oldMission->getInfo());
        }

        $mission = $this->updateStatus($mission);

        // Manage attachment
        $file = $mission->getAttachment();

        if ($file && $user->isGla()) {
            $fileName = $this->fileUploader->upload($file);

            $mission->setAttachment($fileName);
        } elseif ($oldMission->getAttachment()) {
            $mission->setAttachment($oldMission->getAttachment());
        }

//        $mission = $form->getData();

        $this->persist($mission);
    }

    public function assignVolunteer(Mission $mission, User $user): FLashMessage
    {
        $mission->setVolunteer($user)
            ->setDateAssigned(new \DateTime())
            ->setStatus(Constant::STATUS_ASSIGNED);

        self::updateStatus($mission);

        $this->persist($mission);

        return new FLashMessage('La mission vous a bien été attribuée.');
    }

    /**
     * Close or re-open and persist a mission
     *
     * @param Mission $mission
     *
     * @return FLashMessage
     */
    public function close(Mission $mission): FLashMessage
    {
        $status = $mission->getStatus();

        if ($status === Constant::STATUS_FINISHED) {
            $mission->setStatus(Constant::STATUS_CLOSED);

            $flash = new FLashMessage('La fiche mission a bien été fermée.');
        } elseif ($status === Constant::STATUS_CLOSED) {
            $mission->setStatus(Constant::STATUS_FINISHED);

            $flash = new FLashMessage('La fiche mission a bien été ré-ouverte.');
        } else {
            throw new InvalidParameterException('Une mission doit être terminée pour être fermée.', Response::HTTP_BAD_REQUEST);
        }

        $this->persist($mission);

        return $flash;
    }

    public function deleteAttachment(Mission $mission): FLashMessage
    {
        $fileName = $mission->getAttachment();

        if (!empty($fileName)) {
            $this->fileUploader->delete($fileName);

            $mission->setAttachment(null);
            $this->persist($mission);

            $flash = new FLashMessage('La pièce jointe a bien été supprimée.');
        } else {
            $flash = new FLashMessage('Aucune pièce jointe associée à cette mission.', FLashMessage::TYPE_ERROR);
        }

        return $flash;
    }

    public function delete(Mission $mission): FLashMessage
    {
        $this->deleteAttachment($mission);

        $this->entityManager->remove($mission);
        $this->entityManager->flush();

        return new FLashMessage('La fiche mission a bien été supprimée.');
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

    public function getJsonMissions(array $missions): array
    {
        $missionsJson = [];
        /** @var Mission $mission */
        foreach ($missions as $mission) {
            $missionJson = [
                // Path to mission view
                'url' => $this->router->generate('app_mission_view', ['id' => $mission->getId()]),
                // Mission properties
                'id' => $mission->getId(),
                'status' => $mission->getStatus(),
                'dateCreated' => $mission->getFormattedDateCreated(),
                'dateAssigned' => $mission->getDateAssigned() ? $mission->getFormattedDateAssigned() : null,
                'dateFinished' => $mission->getDateFinished() ? $mission->getFormattedDateFinished() : null,
                'description' => $mission->getDescription(),
                'conclusions' => $mission->getConclusions(),
                // Gla
                'gla' => [
                    'id' => $mission->getGla()->getId(),
                    'name' => $mission->getGla()->getName()
                ],
                // Address
                'address' => [
                    'id' => $mission->getAddress()->getId(),
                    'name' => $mission->getAddress()->getName(),
                    'street' => $mission->getAddress()->getStreet(),
                    'zipCode' => $mission->getAddress()->getZipCode(),
                    'city' => $mission->getAddress()->getCity()
                ]
            ];

            if ($mission->getVolunteer()) {
                // Volunteer
                $volunteerJson = ['volunteer' => [
                    'id' => $mission->getVolunteer()->getId(),
                    'name' => $mission->getVolunteer()->getName()
                ]];

                $missionJson = array_merge($missionJson, $volunteerJson);
            }

            array_push($missionsJson, $missionJson);
        }

        return $missionsJson;
    }
}
