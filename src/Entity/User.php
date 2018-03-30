<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
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
    const EMAIL_DOMAIN = '@habitat-humanisme.org';
    // List of possible roles
    // const ROLE_ADMIN = 'ROLE_ADMIN';
    // const ROLE_GLA = 'ROLE_GLA';
    // const ROLE_VOLUNTEER = 'ROLE_VOLUNTEER';

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=25, unique=true)
     * @Assert\NotBlank()
     */
    private $username;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max=4096)
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
     * @Assert\NotBlank()
     */
    private $roles;

    // *
    //  * @ORM\Column(type="string", length=25)
    //  * @Assert\Choice(callback="getCategories")
     
    // private $category;

    /**
     * @ORM\Column(type="string", length=25, unique=true)
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;

    /**
     * Adds (or not) user to dropdown gla "Provenance GLA" in Mission forms
     * @ORM\Column(name="is_gla", type="boolean")
     */
    private $isGla;

    /**
     * Adds (or not) user to dropdown volunteer "Bénévole" in Mission forms
     * @ORM\Column(name="is_volunteer", type="boolean")
     */
    private $isVolunteer;


    /************** Methods *****************/

    public static function getCategories()
    {
        return array(
            'Administrateur' => 'ROLE_ADMIN',
            'GLA' => 'ROLE_GLA',
            'Bénévole' => 'ROLE_VOLUNTEER'
        );
    }

    public function setIsGlaIsVolunteer()
    {
        $roles = $this->getRoles();
        
        // ROLE_GLA (resp. ROLE_VOLUNTEER) is only in Gla list (resp. Volunteer list)
        $this->isGla = in_array('ROLE_ADMIN', $roles) || in_array('ROLE_GLA', $roles);
        $this->isVolunteer = in_array('ROLE_ADMIN', $roles) || in_array('ROLE_VOLUNTEER', $roles);
    }

    public function hasRole($role)
    {
        return in_array($role, $this->roles);
    }

    public function __construct()
    {
        $this->isActive = true;
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


    /********** Getters and setters *************/

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

    public function getCategory()
    {
        return $this->category;
    }

    public function setCategory($category)
    {
        $this->category = $category;
        $this->roles = [$category];
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

    public function setIsGla($isGla)
    {
        $this->isGla = $isGla;
    }

    public function getIsVolunteer()
    {
        return $this->isVolunteer;
    }

    public function setIsVolunteer($isVolunteer)
    {
        $this->isVolunteer = $isVolunteer;
    }
}