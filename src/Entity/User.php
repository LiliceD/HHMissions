<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;

/**
 * @ORM\Table(name="user")
 *
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 *
 * @UniqueEntity(
 *     fields="email",
 *     message="Adresse email déjà utilisée"
 * )
 * @UniqueEntity(
 *     fields="username",
 *     message="Nom d'utilisateur déjà utilisé"
 * )
 */
class User implements AdvancedUserInterface, \Serializable
{
    //  █████╗ ████████╗████████╗██████╗ ██╗██████╗ ██╗   ██╗████████╗███████╗███████╗
    // ██╔══██╗╚══██╔══╝╚══██╔══╝██╔══██╗██║██╔══██╗██║   ██║╚══██╔══╝██╔════╝██╔════╝
    // ███████║   ██║      ██║   ██████╔╝██║██████╔╝██║   ██║   ██║   █████╗  ███████╗
    // ██╔══██║   ██║      ██║   ██╔══██╗██║██╔══██╗██║   ██║   ██║   ██╔══╝  ╚════██║
    // ██║  ██║   ██║      ██║   ██║  ██║██║██████╔╝╚██████╔╝   ██║   ███████╗███████║
    // ╚═╝  ╚═╝   ╚═╝      ╚═╝   ╚═╝  ╚═╝╚═╝╚═════╝  ╚═════╝    ╚═╝   ╚══════╝╚══════╝

    /**
     * User's id
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @var int
     */
    private $id;

    /**
     * User's username to login
     *
     * @ORM\Column(type="string", length=25, unique=true)
     *
     * @Assert\NotBlank()
     * @Assert\Length(max=25)
     *
     * @var string
     */
    private $username;

    /**
     * User's plain password before encrypting
     *
     * @Assert\NotBlank(groups={"Default", "change_password"})
     * @Assert\Length(
     *     min=8,
     *     max=4096,
     *     groups={"Default", "change_password"},
     * )
     *
     * @var string
     */
    private $plainPassword;

    /**
     * User's encrypted password
     *
     * @ORM\Column(type="string", length=64)
     *
     * @var string
     */
    private $password;

    /**
     * User's email address
     *
     * @ORM\Column(type="string", length=254, unique=true)
     *
     * @Assert\NotBlank()
     * @Assert\Email()
     *
     * @var string
     */
    private $email;

    /**
     * User's authorization roles
     *
     * @ORM\Column(type="simple_array")
     *
     * @var array
     */
    private $roles;

    /**
     * User's category (admin, volunteer, gla...)
     *
     * @ORM\Column(type="string", length=50)
     *
     * @Assert\Choice(
     *     callback={"App\Utils\Constant", "getUserCategories"}),
     *     groups={"Default", "edit"},
     * )
     *
     * @var string
     */
    private $category;

    /**
     * User's pole(s) of activity(ies)
     *
     * @ORM\Column(type="simple_array")
     *
     * @Assert\Choice(
     *     callback={"App\Utils\Constant", "getActivities"},
     *     multiple=true,
     *     groups={"Default", "edit"},
     * )
     *
     * @var array
     */
    private $activities;

    /**
     * User's full name
     *
     * @ORM\Column(type="string", length=50)
     *
     * @Assert\NotBlank(groups={"Default", "edit"})
     * @Assert\Length(max=50)
     *
     * @var string
     */
    private $name;

    /**
     * Is the User active?
     *
     * @ORM\Column(name="is_active", type="boolean")
     *
     * @var bool
     */
    private $active;

    /**
     * Can the User create missions?
     *
     * Adds (or not) user to suggestions of gla in 'new mission' form
     *
     * @ORM\Column(name="is_gla", type="boolean")
     *
     * @var bool
     */
    private $gla;

    /**
     * Is the User a volunteer?
     *
     * Adds (or not) user to suggestions of volunteer in 'edit mission' form
     *
     * @ORM\Column(name="is_volunteer", type="boolean")
     *
     * @var bool
     */
    private $volunteer;

    /**
     * All the Missions the User has created
     *
     * @ORM\OneToMany(
     *     targetEntity="App\Entity\Mission",
     *     mappedBy="gla",
     * )
     *
     * @var Collection
     */
    private $missionsAsGla;

    /**
     * All the Missions the User is assigned to
     *
     * @ORM\OneToMany(
     *     targetEntity="App\Entity\Mission",
     *     mappedBy="volunteer",
     * )
     *
     * @var Collection
     */
    private $missionsAsVolunteer;

    // ███╗   ███╗███████╗████████╗██╗  ██╗ ██████╗ ██████╗ ███████╗
    // ████╗ ████║██╔════╝╚══██╔══╝██║  ██║██╔═══██╗██╔══██╗██╔════╝
    // ██╔████╔██║█████╗     ██║   ███████║██║   ██║██║  ██║███████╗
    // ██║╚██╔╝██║██╔══╝     ██║   ██╔══██║██║   ██║██║  ██║╚════██║
    // ██║ ╚═╝ ██║███████╗   ██║   ██║  ██║╚██████╔╝██████╔╝███████║
    // ╚═╝     ╚═╝╚══════╝   ╚═╝   ╚═╝  ╚═╝ ╚═════╝ ╚═════╝ ╚══════╝

    /**
     * Entity validation function
     *
     * @Assert\Callback
     *
     * @param ExecutionContextInterface $context
     */
    public function validate(ExecutionContextInterface $context)
    {
        // Check if email is username@habitat-humanisme.org
        if ($this->getEmail() !== $this->getUsername().'@habitat-humanisme.org') {
            $context->buildViolation('L\'adresse email doit être de la forme votre.identifiant@habitat-humanisme.org.')
                ->atPath('email')
                ->addViolation();
        }
    }

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->active = true;
        $this->missionsAsGla = new ArrayCollection();
        $this->missionsAsVolunteer = new ArrayCollection();
    }

    /**
     * @return string|null
     */
    public function __toString(): ?string
    {
        return $this->name;
    }

    /**
     * Mandatory function
     *
     * @return string|null
     */
    public function getSalt()
    {
        // The bcrypt algorithm doesn't require a separate salt.
        return null;
    }

    /**
     * Mandatory function
     */
    public function eraseCredentials()
    {
    }

    /**
     * Mandatory function
     *
     * @see \Serializable::serialize()
     *
     * @return string
     */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->password,
            $this->active
        ));
    }

    /**
     * Mandatory function
     *
     * @see \Serializable::unserialize()
     *
     * @param string $serialized
     */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->password,
            $this->active
        ) = unserialize($serialized);
    }

    //  █████╗  ██████╗ ██████╗███████╗███████╗███████╗ ██████╗ ██████╗ ███████╗
    // ██╔══██╗██╔════╝██╔════╝██╔════╝██╔════╝██╔════╝██╔═══██╗██╔══██╗██╔════╝
    // ███████║██║     ██║     █████╗  ███████╗███████╗██║   ██║██████╔╝███████╗
    // ██╔══██║██║     ██║     ██╔══╝  ╚════██║╚════██║██║   ██║██╔══██╗╚════██║
    // ██║  ██║╚██████╗╚██████╗███████╗███████║███████║╚██████╔╝██║  ██║███████║
    // ╚═╝  ╚═╝ ╚═════╝ ╚═════╝╚══════╝╚══════╝╚══════╝ ╚═════╝ ╚═╝  ╚═╝╚══════╝

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername(string $username)
    {
        $this->username = $username;
    }

    /**
     * @return string
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * @param string $password
     */
    public function setPlainPassword(string $password)
    {
        $this->plainPassword = $password;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password)
    {
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email)
    {
        $this->email = $email;
    }

    /**
     * Mandatory function
     *
     * @return array
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * @param array $roles
     */
    public function setRoles(array $roles)
    {
        $this->roles = $roles;

        // ROLE_GLA (resp. ROLE_VOLUNTEER) is only in Gla list (resp. Volunteer list)
        // ROLE_ADMIN is in both lists
        $this->gla = in_array('ROLE_ADMIN', $roles) || in_array('ROLE_GLA', $roles);
        $this->volunteer = in_array('ROLE_ADMIN', $roles) || in_array('ROLE_VOLUNTEER', $roles);
    }

    /**
     * @return array
     */
    public function getActivities()
    {
        return $this->activities;
    }

    /**
     * @param array $activities
     */
    public function setActivities(array $activities)
    {
        $this->activities = $activities;
    }

    /**
     * @return string
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param string $category
     */
    public function setCategory(string $category)
    {
        $this->category = $category;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @param bool $active
     */
    public function setActive(bool $active)
    {
        $this->active = $active;
    }

    /**
     * @return bool
     */
    public function isGla()
    {
        return $this->gla;
    }

    /**
     * @return bool
     */
    public function isVolunteer()
    {
        return $this->volunteer;
    }

    /**
     * @return Collection|Mission[]
     */
    public function getMissionsAsGla():Collection
    {
        return $this->missionsAsGla;
    }

    /**
     * @return Collection|Mission[]
     */
    public function getMissionsAsVolunteer():Collection
    {
        return $this->missionsAsVolunteer;
    }

    // Below are required functions for AdvancedUserInterface to prevent inactive users from logging in

    /**
     * @return bool
     */
    public function isAccountNonExpired()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function isAccountNonLocked()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function isCredentialsNonExpired()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->active;
    }
}
