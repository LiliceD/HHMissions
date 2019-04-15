<?php

namespace App\EventListener;

use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
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

    /**
     * LoginListener constructor.
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
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

        $user->setLastLogin(new DateTime());

        $this->em->persist($user);
        $this->em->flush();
    }
}
