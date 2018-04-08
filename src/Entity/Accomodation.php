<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection; // mapping accomodation to missions
use Doctrine\Common\Collections\Collection; // mapping accomodation to missions
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity; // to use @UniqueEntity validation
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="accomodations")
 * @ORM\Entity(repositoryClass="App\Repository\AccomodationRepository")
 * @UniqueEntity(
 *  fields={"street", "city"},
 *  message="Cette adresse existe déjà."
 * )
 */
class Accomodation
{
    // Types of owner for an accomodatoin
    const OWNER_FFH_BUILDING = "FHH - Immeuble";
    const OWNER_FFH_DIFFUSE = "FHH - Logement isolé";
    const OWNER_PRIVATE_BUILDING = "Privé - Immeuble";
    const OWNER_PRIVATE_DIFFUSE = "Privé - Logement isolé";

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", nullable=true, length=25)
     * @Assert\Length(max=25)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\NotBlank()
     * @Assert\Length(max=50)
     */
    private $street;

    /**
     * @ORM\Column(type="integer", length=5)
     * @Assert\NotBlank()
     * @Assert\Length(
     *  min = 5,
     *  max = 5,
     *  minMessage = "Le code postal doit avoir 5 chiffres.",
     *  maxMessage = "Le code postal doit avoir 5 chiffres." 
     * )
     */
    private $postalCode;

    /**
     * @ORM\Column(type="string", length=25)
     * @Assert\NotBlank()
     * @Assert\Length(max=25)
     */
    private $city;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\Choice(callback="getOwnerTypes")
     */
    private $ownerType;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\Length(max=255)
     */
    private $access;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Mission", mappedBy="accomodation")
     */
    private $missions;


    /************** Methods *****************/

    // Callback for $ownerType Choice
    public static function getOwnerTypes()
    {
        return array(
            Self::OWNER_FFH_BUILDING => Self::OWNER_FFH_BUILDING,
            Self::OWNER_FFH_DIFFUSE => Self::OWNER_FFH_DIFFUSE,
            Self::OWNER_PRIVATE_BUILDING => Self::OWNER_PRIVATE_BUILDING,
            Self::OWNER_PRIVATE_DIFFUSE => Self::OWNER_PRIVATE_DIFFUSE
        );
    }

    // Full address
    public function getAddress()
    {
        return $this->street . ' ' . ($this->name ? '(' . $this->name . ') ' : '') . $this->postalCode . ' ' . $this->city;
    }


    public function __construct()
    {
        $this->missions = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->address;
    }


    /********** Getters and setters *************/

    public function getId()
    {
        return $this->id;
    }


    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }


    public function getStreet()
    {
        return $this->street;
    }

    public function setStreet($street)
    {
        $this->street = $street;
    }


    public function getPostalCode()
    {
        return $this->postalCode;
    }

    public function setPostalCode($postalCode)
    {
        $this->postalCode = $postalCode;
    }


    public function getCity()
    {
        return $this->city;
    }

    public function setCity($city)
    {
        $this->city = $city;
    }


    public function getOwnerType()
    {
        return $this->ownerType;
    }

    public function setOwnerType($ownerType)
    {
        $this->ownerType = $ownerType;
    }

    public function getAccess()
    {
        return $this->access;
    }

    public function setAccess($access)
    {
        $this->access = $access;
    }


    /**
     * @return Collection|Mission[]
     */
    public function getMissions()
    {
        return $this->missions;
    }
}
