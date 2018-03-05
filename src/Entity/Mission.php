<?php

namespace App\Entity;

// use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM; // this use statement is needed for the annotations @ORM
use Symfony\Component\Validator\Constraints as Assert; // this use statement is needed for the annotations @Assert

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
     * 
     * @Assert\Choice(callback="getStatuses")
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Accomodation", inversedBy="missions")
     * @ORM\JoinColumn(nullable=true)
     */
    private $accomodation;

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
     * @Assert\NotBlank(message="Merci de remplir la description de la mission")
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

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     * @Assert\File(mimeTypes={ "application/pdf" })
     */
    private $attachment;


    /************** Functions *****************/

    // List of possible statuses for a mission
    public static function getStatuses()
    {
        return array(
            // getStatuses()[0] :
            'Créée',    // Default status when mission is created (cf __construct())
            // getStatuses()[1] :
            'Publiée',  // When mission information has been completed by Admin
            // getStatuses()[2] :
            'Prise en charge',  // When a volunteer got assigned to the mission (cf dateAssigned)
            // getStatuses()[3] :
            'Terminée', // When the mission has been done (cf dateFinished, conclusions)
            // getStatuses()[4 / length-1] :
            'Fermée'    // When Admin reviewed conclusions and officialy closed mission (cf isClosed())
        );
    }
    // // Returns true if and only if status = "Fermée"
    // public function isClosed()
    // {
    //     return $this->status === 'Fermée';
    // }
    
    public function __construct()
    {
        $this->status = $this->getStatuses()[0];
        $this->dateCreated = new \DateTime();
    }


    /********** Getters and setters *************/

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

    public function getAccomodation(): Accomodation
    {
        return $this->accomodation;
    }
    public function setAccomodation(Accomodation $accomodation)
    {
        $this->accomodation = $accomodation;
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
    
    public function getAttachment()
    {
        return $this->attachment;
    }
    public function setAttachment($attachment)
    {
        $this->attachment = $attachment;

        return $this;
    }
}