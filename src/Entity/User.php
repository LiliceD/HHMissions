<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection; // mapping user to missions
use Doctrine\Common\Collections\Collection; // mapping user to missions
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface; // to use Callback validation
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(fields="email", message="Adresse email déjà utilisée")
 * @UniqueEntity(fields="username", message="Nom d'utilisateur déjà utilisé")
 */
class User implements UserInterface, \Serializable
{
    // Categories (cf getCategory() to see relation to roles)
    const CAT_ADMIN = 'Admin';
    const CAT_GLA = 'GLA';
    const CAT_VOLUNTEER = 'Bénévole';

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=25, unique=true)
     * @Assert\NotBlank()
     * @Assert\Length(max=25)
     */
    private $username;

    /**
     * @Assert\NotBlank(groups={"Default", "change_password"})
     * @Assert\Length(min=8, max=4096, groups={"Default", "change_password"})
     */
    private $plainPassword;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=254, unique=true)
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    private $email;

    /**
     * @ORM\Column(type="simple_array")
     */
    private $roles;

    /**
     * @ORM\Column(type="string", length=25)
     * @Assert\NotBlank(groups={"Default", "edit"})
     * @Assert\Length(max=25)
     */
    private $name;

    /**
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;

    /**
     * Adds (or not) user to suggestions of gla in 'new mission' form
     * @ORM\Column(name="is_gla", type="boolean")
     */
    private $isGla;

    /**
     * Adds (or not) user to suggestions of volunteer in 'edit mission' form
     * @ORM\Column(name="is_volunteer", type="boolean")
     */
    private $isVolunteer;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Mission", mappedBy="gla")
     */
    private $missionsAsGla;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Mission", mappedBy="volunteer")
     */
    private $missionsAsVolunteer;


    /**
     * @Assert\Callback
     */
    public function validate(ExecutionContextInterface $context, $payload)
    {
        // Check if email is username@habitat-humanisme.org
        if ($this->getEmail() !== $this->getUsername().'@habitat-humanisme.org') {
            $context->buildViolation('L\'adresse email doit être égale à identifiant@habitat-humanisme.org.')
                ->atPath('email')
                ->addViolation();
        }
    }

    /************** Public methods *****************/

    // Category dropdown in UserType
    public static function getCategories()
    {
        return [
            Self::CAT_ADMIN => 'ROLE_ADMIN',
            Self::CAT_GLA => 'ROLE_GLA',
            Self::CAT_VOLUNTEER => 'ROLE_VOLUNTEER'
        ];
    }

    // Mapping roles to category
    public function getCategory() {
        $roles = $this->roles;

        if (in_array('ROLE_ADMIN', $roles)) {
            $category = Self::CAT_ADMIN;
        } else if (in_array('ROLE_VOLUNTEER', $roles)) {
            // User with ROLE_VOLUNTEER and ROLE_GLA is in Volunteers category
            $category = Self::CAT_VOLUNTEER;
        } else if (in_array('ROLE_GLA', $roles)) {
            $category = Self::CAT_GLA;
        }

        return $category;
    }

    public function setRolesFromCategory($category, $hasGlaRole = false)
    {
        $roles = [$category];

        if ($category === 'ROLE_VOLUNTEER' && $hasGlaRole) {
            // Add 'ROLE_GLA' to volunteers allowed to create missions
            array_push($roles, 'ROLE_GLA');
        }

        $this->setRoles($roles);
    }


    public function __construct()
    {
        $this->isActive = true;
        $this->missionsAsGla = new ArrayCollection();
        $this->missionsAsVolunteer = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->name;
    }


    // Mandatory function
    public function getSalt()
    {
        // The bcrypt algorithm doesn't require a separate salt.
        return null;
    }

    // Mandatory function
    public function eraseCredentials()
    {
    }

    // Mandatory function
    /** @see \Serializable::serialize() */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->password,
        ));
    }

    // Mandatory function
    /** @see \Serializable::unserialize() */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->password,
        ) = unserialize($serialized);
    }


    /********** Private functions *************/

    private function setIsGlaIsVolunteer()
    {
        $roles = $this->getRoles();
        
        // ROLE_GLA (resp. ROLE_VOLUNTEER) is only in Gla list (resp. Volunteer list)
        // ROLE_ADMIN is in both lists
        $this->isGla = in_array('ROLE_ADMIN', $roles) || in_array('ROLE_GLA', $roles);
        $this->isVolunteer = in_array('ROLE_ADMIN', $roles) || in_array('ROLE_VOLUNTEER', $roles);
    }


    /********** Getters and setters *************/

    public function getId()
    {
        return $this->id;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }


    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    public function setPlainPassword($password)
    {
        $this->plainPassword = $password;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }


    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }


    // Mandatory function
    public function getRoles()
    {
        return $this->roles;
    }

    public function setRoles($roles)
    {
        $this->roles = $roles;
        $this->setIsGlaIsVolunteer();
    }


    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }


    public function getIsGla()
    {
        return $this->isGla;
    }

    public function getIsVolunteer()
    {
        return $this->isVolunteer;
    }


    /**
     * @return Collection|Mission[]
     */
    public function getMissionsAsGla()
    {
        return $this->missionsAsGla;
    }

    /**
     * @return Collection|Mission[]
     */
    public function getMissionsAsVolunteer()
    {
        return $this->missionsAsVolunteer;
    }
}
