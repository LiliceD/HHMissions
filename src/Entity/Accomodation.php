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
    private $address;

    /**
     * @ORM\Column(type="string")
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


    public function getAddress()
    {
        return $this->address;
    }

    public function setAddress($address)
    {
        $this->address = $address;
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


    public function __construct()
    {
        $this->missions = new ArrayCollection();
    }

    public function __toString()
    {
	    return $this->address;
	}
}
