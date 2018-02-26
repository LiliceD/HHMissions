<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection; // mapping accomodation to missions
use Doctrine\Common\Collections\Collection; // mapping accomodation to missions
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AccomodationRepository")
 */
class Accomodation
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     *
     * @Assert\NotBlank()
     */
    private $street;

    /**
     * @ORM\Column(type="integer")
     * 
     * @Assert\NotBlank()
     */
    private $postalCode;

    /**
     * @ORM\Column(type="string")
     * 
     * @Assert\NotBlank()
     */
    private $city;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $access;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Mission", mappedBy="accomodation")
     */
    private $missions;


    public function getId()
    {
        return $this->id;
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


    // Full address
    public function getAddress()
    {
        return $this->street . ' ' . $this->postalCode . ' ' . $this->city;
    }

    // public function setAddress($address)
    // {
    //     $this->address = $address;
    // }


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


    public function __construct()
    {
        $this->missions = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->address;
    }
}
