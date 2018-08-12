<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection; // mapping address to missions
use Doctrine\Common\Collections\Collection; // mapping address to missions
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity; // to use @UniqueEntity validation
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="address")
 * @ORM\Entity(repositoryClass="App\Repository\AddressRepository")
 * @UniqueEntity(
 *  fields={"street", "city"},
 *  message="Cette adresse existe déjà."
 * )
 */
class Address
{
    /**
     * Address's id
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * Address's name (e.g. for a residence)
     *
     * @ORM\Column(type="string", nullable=true, length=25)
     * @Assert\Length(max=25)
     */
    private $name;

    /**
     * Address's street name and number
     *
     * @ORM\Column(type="string", length=50)
     * @Assert\NotBlank()
     * @Assert\Length(max=50)
     */
    private $street;

    /**
     * Address's zip code
     *
     * @ORM\Column(type="integer", length=5)
     * @Assert\NotBlank()
     * @Assert\Length(
     *  min = 5,
     *  max = 5,
     *  minMessage = "Le code postal doit avoir 5 chiffres.",
     *  maxMessage = "Le code postal doit avoir 5 chiffres."
     * )
     */
    private $zipCode;

    /**
     * Address's city
     *
     * @ORM\Column(type="string", length=25)
     * @Assert\NotBlank()
     * @Assert\Length(max=25)
     */
    private $city;

    /**
     * Address's type (building / single apartment) and owner (FHH / private)
     *
     * @ORM\Column(type="string", nullable=true)
     * @Assert\Choice(callback={"App\Utils\Constant", "getOwnerTypes"})
     */
    private $ownerType;

    /**
     * Address's access details (e.g. entry system)
     *
     * @ORM\Column(type="string", nullable=true)
     * @Assert\Length(max=255)
     */
    private $access;

    /**
     * All Missions taking place at this Address
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Mission", mappedBy="address")
     */
    private $missions;


    /************** Methods *****************/

    /**
     * Address constructor.
     */
    public function __construct()
    {
        $this->missions = new ArrayCollection();
    }

    /**
     * @return string|null
     */
    public function __toString(): ?string
    {
        return $this->street;
    }

    /**
     * Full address
     *
     * @return string|null
     */
    public function getAddress(): ?string
    {
        return $this->street . ' '
            . ($this->name ? '(' . $this->name . ') ' : '')
            . $this->zipCode . ' ' . $this->city;
    }

    /********** Getters and setters *************/

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
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
     * @return string|null
     */
    public function getStreet(): ?string
    {
        return $this->street;
    }

    /**
     * @param string $street
     */
    public function setStreet(string $street)
    {
        $this->street = $street;
    }

    /**
     * @return int|null
     */
    public function getZipCode(): ?int
    {
        return $this->zipCode;
    }

    /**
     * @param int $zipCode
     */
    public function setZipCode(int $zipCode)
    {
        $this->zipCode = $zipCode;
    }

    /**
     * @return string|null
     */
    public function getCity(): ?string
    {
        return $this->city;
    }

    /**
     * @param string $city
     */
    public function setCity(string $city)
    {
        $this->city = $city;
    }

    /**
     * @return string|null
     */
    public function getOwnerType(): ?string
    {
        return $this->ownerType;
    }

    /**
     * @param string $ownerType
     */
    public function setOwnerType(string $ownerType)
    {
        $this->ownerType = $ownerType;
    }

    /**
     * @return string|null
     */
    public function getAccess(): ?string
    {
        return $this->access;
    }

    /**
     * @param string $access
     */
    public function setAccess(string $access)
    {
        $this->access = $access;
    }

    /**
     * @return Collection|Mission[]|null
     */
    public function getMissions()
    {
        return $this->missions;
    }
}
