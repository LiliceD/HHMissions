<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MissionRepository")
 */
class Mission
{
    const NUM_ITEMS = 10;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $status;

    /**
     * @ORM\Column(type="string")
     *
     * @Assert\NotBlank()
     */
    private $address;

     /**
     * @ORM\Column(type="string")
     *
     * @Assert\NotBlank()
     */
    private $gla;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $volunteer;

    /**
     * @ORM\Column(type="text")
     *
     * @Assert\NotBlank(message="Merci de remplir la mission")
     */
    private $description;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $info;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $conclusions;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateCreated;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateAssigned;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateFinished;


    public function __construct()
    {
        $this->status = "created";
        $this->dateCreated = new \DateTime();
    }


    public function getId()
    {
        return $this->id;
    }


    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }


    public function getAddress()
    {
        return $this->address;
    }

    public function setAddress($address)
    {
        $this->address = $address;
    }


    public function getGla()
    {
        return $this->gla;
    }

    public function setGla($gla)
    {
        $this->gla = $gla;
    }


    public function getVolunteer()
    {
        return $this->volunteer;
    }

    public function setVolunteer($volunteer)
    {
        $this->volunteer = $volunteer;
    }


    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }


    public function getInfo()
    {
        return $this->info;
    }

    public function setInfo($info)
    {
        $this->info = $info;
    }


    public function getConclusions()
    {
        return $this->conclusions;
    }

    public function setConclusions($conclusions)
    {
        $this->conclusions = $conclusions;
    }


    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    public function setDateCreated($dateCreated)
    {
        $this->dateCreated = $dateCreated;
    }


    public function getDateAssigned()
    {
        return $this->dateAssigned;
    }

    public function setDateAssigned($dateAssigned)
    {
        $this->dateAssigned = $dateAssigned;
    }


    public function getDateFinished()
    {
        return $this->dateFinished;
    }

    public function setDateFinished($dateFinished)
    {
        $this->dateFinished = $dateFinished;
    }
    // getters and setters ...
}