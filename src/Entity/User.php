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
 * Class User
 *
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
 *
 * @author Alice Dahan <lilice.dhn@gmail.com>
 */
class User implements AdvancedUserInterface, \Serializable
{
    const ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN';
    const ROLE_ADMIN = 'ROLE_ADMIN';
    const ROLE_GLA = 'ROLE_GLA';
    const ROLE_VOLUNTEER = 'ROLE_VOLUNTEER';

    const CATEGORY_ADMIN = 'Admin';
    const CATEGORY_GLA = 'GLA';
    const CATEGORY_VOLUNTEER = 'Bénévole';

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
    private $roles = [];

    /**
     * User's category (admin, volunteer, gla...)
     *
     * @ORM\Column(type="string", length=50)
     *
     * @Assert\Choice(
     *     callback="getCategories"),
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
    private $activities = [];

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
    private $active = true;

    /**
     * Can the User create missions?
     *
     * Adds (or not) user to suggestions of gla in 'new mission' form
     *
     * @ORM\Column(name="is_gla", type="boolean")
     *
     * @var bool
     */
    private $gla = false;

    /**
     * Is the User a volunteer?
     *
     * Adds (or not) user to suggestions of volunteer in 'edit mission' form
     *
     * @ORM\Column(name="is_volunteer", type="boolean")
     *
     * @var bool
     */
    private $volunteer = false;

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

    /**
     * All the Addresses the User is in charge of if they are a GLA
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Address", mappedBy="gla")
     *
     * @var Collection
     */
    private $addressesAsGla;

    /**
     * All the buildings'Addresses the User is referent for if they are a volunteer
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Address", mappedBy="buildingManager")
     *
     * @var Collection
     */
    private $buildingsAsReferent;

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
        $this->missionsAsGla = new ArrayCollection();
        $this->missionsAsVolunteer = new ArrayCollection();
        $this->addressesAsGla = new ArrayCollection();
        $this->buildingsAsReferent = new ArrayCollection();
    }

    /**
     * @return string|null
     */
    public function __toString(): ?string
    {
        return $this->name;
    }

    /**
     * Callback for User->$category
     * Category dropdown in UserType
     *
     * @return array
     */
    public static function getCategories(): array
    {
        return [
            self::CATEGORY_VOLUNTEER => self::CATEGORY_VOLUNTEER,
            self::CATEGORY_GLA => self::CATEGORY_GLA,
            self::CATEGORY_ADMIN => self::CATEGORY_ADMIN,
        ];
    }

    /**
     * Category to role mapping
     *
     * @return array
     */
    public static function getRolesFromCategories(): array
    {
        return [
            self::CATEGORY_VOLUNTEER => [self::ROLE_VOLUNTEER],
            self::CATEGORY_GLA => [self::ROLE_GLA],
            self::CATEGORY_ADMIN => [self::ROLE_ADMIN],
        ];
    }

    /**
     * Mandatory function
     *
     * @return null
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
    public function serialize(): string
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
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * @param string $username
     *
     * @return User
     */
    public function setUsername(string $username): User
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return string
     */
    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    /**
     * @param string $password
     *
     * @return User
     */
    public function setPlainPassword(string $password): User
    {
        $this->plainPassword = $password;

        return $this;
    }

    /**
     * @return string
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * @param string $password
     *
     * @return User
     */
    public function setPassword(string $password): User
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string $email
     *
     * @return User
     */
    public function setEmail(string $email): User
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Mandatory function
     *
     * @return array
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * @param array $roles
     *
     * @return User
     */
    public function setRoles(array $roles): User
    {
        $this->roles = $roles;

        // ROLE_GLA (resp. ROLE_VOLUNTEER) is only in Gla list (resp. Volunteer list)
        // ROLE_ADMIN is in both lists
        $this->gla = \in_array(self::ROLE_ADMIN, $roles) || \in_array(self::ROLE_GLA, $roles);
        $this->volunteer = \in_array(self::ROLE_ADMIN, $roles) || \in_array(self::ROLE_VOLUNTEER, $roles);

        return $this;
    }

    /**
     * @return array
     */
    public function getActivities(): array
    {
        return $this->activities;
    }

    /**
     * @param array $activities
     *
     * @return User
     */
    public function setActivities(array $activities): User
    {
        $this->activities = $activities;

        return $this;
    }

    /**
     * @return string
     */
    public function getCategory(): ?string
    {
        return $this->category;
    }

    /**
     * @param string $category
     *
     * @return User
     */
    public function setCategory(string $category): User
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return User
     */
    public function setName(string $name): User
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * @param bool $active
     *
     * @return User
     */
    public function setActive(bool $active): User
    {
        $this->active = $active;

        return $this;
    }

    /**
     * @return bool
     */
    public function isGla(): bool
    {
        return $this->gla;
    }

    /**
     * @return bool
     */
    public function isVolunteer(): bool
    {
        return $this->volunteer;
    }

    /**
     * @return bool
     */
    public function isAdmin(): bool
    {
        return in_array(User::ROLE_ADMIN, $this->roles);
    }

    /**
     * @return Collection
     */
    public function getMissionsAsGla(): Collection
    {
        return $this->missionsAsGla;
    }

    /**
     * @return Collection
     */
    public function getMissionsAsVolunteer(): Collection
    {
        return $this->missionsAsVolunteer;
    }

    /**
     * @return Collection|Address[]
     */
    public function getAddressesAsGla(): Collection
    {
        return $this->addressesAsGla;
    }

    public function addAddressesAsGla(Address $addressesAsGla): self
    {
        if (!$this->addressesAsGla->contains($addressesAsGla)) {
            $this->addressesAsGla[] = $addressesAsGla;
            $addressesAsGla->setGla($this);
        }

        return $this;
    }

    public function removeAddressesAsGla(Address $addressesAsGla): self
    {
        if ($this->addressesAsGla->contains($addressesAsGla)) {
            $this->addressesAsGla->removeElement($addressesAsGla);
            // set the owning side to null (unless already changed)
            if ($addressesAsGla->getGla() === $this) {
                $addressesAsGla->setGla(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Address[]
     */
    public function getBuildingsAsReferent(): Collection
    {
        return $this->buildingsAsReferent;
    }

    public function addBuildingsAsReferent(Address $buildingsAsReferent): self
    {
        if (!$this->buildingsAsReferent->contains($buildingsAsReferent)) {
            $this->buildingsAsReferent[] = $buildingsAsReferent;
            $buildingsAsReferent->setReferent($this);
        }

        return $this;
    }

    public function removeBuildingsAsReferent(Address $buildingsAsReferent): self
    {
        if ($this->buildingsAsReferent->contains($buildingsAsReferent)) {
            $this->buildingsAsReferent->removeElement($buildingsAsReferent);
            // set the owning side to null (unless already changed)
            if ($buildingsAsReferent->getReferent() === $this) {
                $buildingsAsReferent->setReferent(null);
            }
        }

        return $this;
    }

    // Below are required functions for AdvancedUserInterface to prevent inactive users from logging in

    /**
     * @return bool
     */
    public function isAccountNonExpired(): bool
    {
        return true;
    }

    /**
     * @return bool
     */
    public function isAccountNonLocked(): bool
    {
        return true;
    }

    /**
     * @return bool
     */
    public function isCredentialsNonExpired(): bool
    {
        return true;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->active;
    }
}
