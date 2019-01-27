<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Address
 *
 * @ORM\Table(name="address")
 *
 * @ORM\Entity(repositoryClass="App\Repository\AddressRepository")
 *
 * @UniqueEntity(
 *  fields={"street", "city"},
 *  message="Cette adresse existe déjà."
 * )
 *
 * @author Alice Dahan <lilice.dhn@gmail.com>
 */
class Address
{
    const OWNER_FFH_BUILDING = "FHH - Immeuble";
    const OWNER_FFH_DIFFUSE = "FHH - Logement isolé";
    const OWNER_PRIVATE_BUILDING = "Privé - Immeuble";
    const OWNER_PRIVATE_DIFFUSE = "Privé - Logement isolé";

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
     * @Assert\Choice(callback="getOwnerTypes")
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
     * @return string
     */
    public function __toString(): string
    {
        return $this->street . ' '
            . ($this->name ? '(' . $this->name . ') ' : '')
            . $this->zipCode . ' ' . $this->city ?? '';
    }

    /**
     * Callback for Address->$ownerType
     *
     * @return array
     */
    public static function getOwnerTypes()
    {
        return array(
            self::OWNER_FFH_BUILDING => self::OWNER_FFH_BUILDING,
            self::OWNER_FFH_DIFFUSE => self::OWNER_FFH_DIFFUSE,
            self::OWNER_PRIVATE_BUILDING => self::OWNER_PRIVATE_BUILDING,
            self::OWNER_PRIVATE_DIFFUSE => self::OWNER_PRIVATE_DIFFUSE
        );
    }

    /**
     * Full address
     *
     * @return string
     */
    public function getAddress(): string
    {
        return $this->__toString();
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
     *
     * @return Address
     */
    public function setName(string $name): Address
    {
        $this->name = $name;

        return $this;
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
     *
     * @return Address
     */
    public function setStreet(string $street): Address
    {
        $this->street = $street;

        return $this;
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
     *
     * @return Address
     */
    public function setZipCode(int $zipCode): Address
    {
        $this->zipCode = $zipCode;

        return $this;
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
     *
     * @return Address
     */
    public function setCity(string $city): Address
    {
        $this->city = $city;

        return $this;
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
     *
     * @return Address
     */
    public function setOwnerType(string $ownerType): Address
    {
        $this->ownerType = $ownerType;

        return $this;
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
     *
     * @return Address
     */
    public function setAccess(string $access): Address
    {
        $this->access = $access;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getMissions(): Collection
    {
        return $this->missions;
    }
}
