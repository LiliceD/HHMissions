<?php

namespace App\EventListener;

use App\Entity\Mission;
use App\Manager\MissionManager;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use App\Entity\User;

/**
 * Class LoginListener
 *
 * @author Alice Dahan <lilice.dhn@gmail.com>
 */
class LoginListener
{
    /** @var EntityManagerInterface $em */
    private $em;

    /** @var MissionManager $missionManager */
    private $missionManager;

    /** @var SessionInterface $session */
    private $session;

    /**
     * LoginListener constructor.
     *
     * @param EntityManagerInterface $em
     * @param MissionManager         $missionManager
     * @param SessionInterface       $session
     */
    public function __construct(EntityManagerInterface $em, MissionManager $missionManager, SessionInterface $session)
    {
        $this->em = $em;
        $this->missionManager = $missionManager;
        $this->session = $session;
    }

    /**
     * @param InteractiveLoginEvent $event
     *
     * @throws Exception
     */
    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event): void
    {
        /** @var User $user */
        $user = $event->getAuthenticationToken()->getUser();
        $updatedMissions = $this->missionManager->getMissionsUpdatedSinceUserLastLogin($user);

        $this->storeUpdatedMissionsIdsInSession($updatedMissions);

        $this->updateLastLogin($user);
    }

    /**
     * @param array $updatedMissions
     */
    private function storeUpdatedMissionsIdsInSession(array $updatedMissions)
    {
        /** @var ArrayCollection $missionsIds */
        $missionsIds = (new ArrayCollection($updatedMissions))->map(function ($mission) {
            /** @var Mission $mission */
            return $mission->getId();
        });

        $this->session->set('updatedMissions', $missionsIds->getValues());
    }

    /**
     * @param User $user
     *
     * @throws Exception
     */
    private function updateLastLogin(User $user): void
    {
        $user->setLastLogin(new DateTime());

        $this->em->persist($user);
        $this->em->flush();
    }
}
