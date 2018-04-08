<?php

namespace App\Security;

use App\Entity\Mission;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface; // to check roles

class MissionVoter extends Voter
{
    const SIMPLE_EDIT = 'simpleEdit';
    const EDIT = 'edit';
    const ASSIGN = 'assign';

    private $decisionManager;

    public function __construct(AccessDecisionManagerInterface $decisionManager)
    {
        $this->decisionManager = $decisionManager;
    }

    protected function supports($attribute, $subject)
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, array(self::SIMPLE_EDIT, self::EDIT, self::ASSIGN))) {
            return false;
        }

        // only vote on Mission objects inside this voter
        if (!$subject instanceof Mission) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            // the user must be logged in; if not, deny access
            return false;
        }

        // ROLE_ADMIN can do anything! The power!
        if ($this->decisionManager->decide($token, array('ROLE_ADMIN'))) {
            return true;
        }

        // you know $subject is a Mission object, thanks to supports
        /** @var Mission $mission */
        $mission = $subject;

        switch ($attribute) {
            case self::SIMPLE_EDIT:
                return $this->canSimpleEdit($mission, $user, $token);
            case self::EDIT:
                return $this->canEdit($mission, $user, $token);
            case self::ASSIGN:
                return $this->canAssign($mission, $user, $token);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canSimpleEdit(Mission $mission, User $user, TokenInterface $token)
    {
        // if mission is closed it can't be edited
        if ($mission->getStatus() === Mission::STATUS_CLOSED) {
        	return false;
        }

        // if they can edit, they can "simple edit"
        if ($this->canEdit($mission, $user, $token)) {
            return true;
        }

        // only the volunteer assigned to the mission can simple edit it
        return $this->decisionManager->decide($token, array('ROLE_VOLUNTEER')) && $user === $mission->getVolunteer();
    }

    private function canEdit(Mission $mission, User $user, TokenInterface $token)
    {
        // only the GLA who created the mission can edit it
        return $this->decisionManager->decide($token, array('ROLE_GLA')) && $user === $mission->getGla();
    }

    private function canAssign(Mission $mission, User $user, TokenInterface $token)
    {
        // only a Volunteer can assign themselves a mission not assigned yet
        return $mission->getStatus() === Mission::STATUS_DEFAULT && $this->decisionManager->decide($token, array('ROLE_VOLUNTEER'));
    }
}