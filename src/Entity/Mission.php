<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM; // this use statement is needed for the annotations @ORM
use Symfony\Component\Validator\Constraints as Assert; // this use statement is needed for the annotations @Assert

/**
 * @ORM\Table(name="missions")
 * @ORM\Entity(repositoryClass="App\Repository\MissionRepository")
 */
class Mission
{
    // Possible statuses for a mission
    const STATUS_DEFAULT = "Créée";
    const STATUS_ASSIGNED = "Prise en charge";
    const STATUS_FINISHED = "Terminée";
    const STATUS_CLOSED = "Fermée";

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
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
     * @Assert\NotBlank()
     */
    private $gla;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $volunteer;

    /**
     * @ORM\Column(type="text")
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
     * @ORM\Column(name="date_created", type="date")
     */
    private $dateCreated;

    /**
     * @ORM\Column(name="date_assigned", type="date", nullable=true)
     */
    private $dateAssigned;

    /**
     * @ORM\Column(name="date_finished", type="date", nullable=true)
     */
    private $dateFinished;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\File(mimeTypes={ "application/pdf" })
     */
    private $attachment;

    
    /************** Methods *****************/
    
    // Callback for $status Choice
    public static function getStatuses()
    {
        return array(
            'default' => Self::STATUS_DEFAULT,    // Default status when mission is created (cf __construct())
            'assigned' => Self::STATUS_ASSIGNED,  // When a volunteer got assigned to the mission (cf dateAssigned)
            'finished' => Self::STATUS_FINISHED, // When the mission has been done (cf dateFinished, conclusions)
            'closed' => Self::STATUS_CLOSED    // When Admin reviewed conclusions and officialy closed mission
        );
    } 

    public function updateStatus()
    {
        if ($this->dateFinished && $this->status !== Self::STATUS_FINISHED) {
            $this->status = Self::STATUS_FINISHED;
        }
        else if ($this->dateAssigned && $this->status !== Self::STATUS_ASSIGNED) {
            $this->status = Self::STATUS_ASSIGNED;
        }
        else if ($this->status !== Self::STATUS_DEFAULT) {
            $this->status = Self::STATUS_DEFAULT;  // Shouldn't happen
        }
    }
    
    public function __construct()
    {
        $this->status = $this->getStatuses()['default'];
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