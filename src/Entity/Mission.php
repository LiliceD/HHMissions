<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM; // this use statement is needed for the annotations @ORM
use Symfony\Component\Validator\Constraints as Assert; // this use statement is needed for the annotations @Assert
use Symfony\Component\Validator\Context\ExecutionContextInterface; // to use Callback validation

/**
 * @ORM\Table(name="missions")
 * @ORM\Entity(repositoryClass="App\Repository\MissionRepository")
 */
class Mission
{
    // Possible statuses for a mission
    const STATUS_DEFAULT = 'Créée';
    const STATUS_ASSIGNED = 'Prise en charge';
    const STATUS_FINISHED = 'Terminée';
    const STATUS_CLOSED = 'Fermée';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=25)
     * @Assert\NotBlank()
     * @Assert\Choice(callback="getStatuses")
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Accomodation", inversedBy="missions")
     * @ORM\JoinColumn()
     */
    private $accomodation;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="missionsAsGla")
     * @ORM\JoinColumn()
     * @Assert\NotBlank()
     * @Assert\Expression(
     *     "value.getIsGla()",
     *     message="Cette personne n'est pas dans la catégorie GLA."
     * )
     */
    private $gla;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="missionsAsVolunteer")
     * @ORM\JoinColumn(nullable=true)
     * @Assert\Expression(
     *     "!value or value.getIsVolunteer()",
     *     message="Cette personne n'est pas dans la catégorie bénévole."
     * )
     */
    private $volunteer;

    /**
     * @ORM\Column(type="string", length=3000)
     * @Assert\NotBlank(message="Veuillez remplir la description de la mission")
     * @Assert\Length(max=3000)
     */
    private $description;

    /**
     * @ORM\Column(type="string", nullable=true, length=1000)
     * @Assert\Length(max=1000)
     */
    private $info;

    /**
     * @ORM\Column(type="string", nullable=true, length=3000)
     * @Assert\Length(max=3000)
     */
    private $conclusions;

    /**
     * @ORM\Column(name="date_created", type="date")
     * @Assert\NotBlank()
     * @Assert\LessThanOrEqual("today", message="La date de demande ne peut pas être dans le futur.")
     */
    private $dateCreated;

    /**
     * @ORM\Column(name="date_assigned", type="date", nullable=true)
     * @Assert\LessThanOrEqual("today", message="La date de prise en charge ne peut pas être dans le futur.")
     * @Assert\Expression(
     *     "!value or value >= this.getDateCreated()",
     *     message="La date de prise en charge doit être après la date de demande."
     * )
     */
    private $dateAssigned;

    /**
     * @ORM\Column(name="date_finished", type="date", nullable=true)
     * @Assert\LessThanOrEqual("today", message="La date de fin de mission ne peut pas être dans le futur.")
     * @Assert\Expression(
     *     "!value or value >= this.getDateAssigned()",
     *     message="La date de fin de mission doit être après la date de prise en charge."
     * )
     */
    private $dateFinished;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\File(mimeTypes={ "application/pdf" })
     */
    private $attachment;

    
    /**
     * @Assert\Callback
     */
    public function validate(ExecutionContextInterface $context, $payload)
    {
        // A mission with a dateAssigned must have a volunteer
        if ($this->getDateAssigned() && !$this->getVolunteer()) {
            $context->buildViolation('Une mission avec une date de prise en charge doit avoir un·e bénévole.')
                ->atPath('volunteer')
                ->addViolation();
        }

        // ... and vice-versa
        if (!$this->getDateAssigned() && $this->getVolunteer()) {
            $context->buildViolation('Une mission avec un·e bénévole doit avoir une date de prise en charge.')
                ->atPath('dateAssigned')
                ->addViolation();
        }

        // A mission with a dateFinished must have conclusions
        if ($this->getDateFinished() && !$this->getDateAssigned()) {
            $context->buildViolation('Une mission doit être prise en charge avant d\'être terminée.')
                ->atPath('dateAssigned')
                ->addViolation();
        }

        // A mission with a dateFinished must have conclusions
        if ($this->getDateFinished() && !$this->getConclusions()) {
            $context->buildViolation('Une mission avec une date de fin doit avoir un retour.')
                ->atPath('conclusions')
                ->addViolation();
        }
    }


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
        if ($this->dateFinished) {
            $this->status = Self::STATUS_FINISHED;
        } else if ($this->dateAssigned) {
            $this->status = Self::STATUS_ASSIGNED;
        } else {
            $this->status = Self::STATUS_DEFAULT; 
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

    public function getAccomodation(): ?Accomodation
    {
        return $this->accomodation;
    }
    public function setAccomodation(Accomodation $accomodation)
    {
        $this->accomodation = $accomodation;
    }

    public function getGla(): ?User
    {
        return $this->gla;
    }
    public function setGla(User $gla)
    {
        $this->gla = $gla;
    }

    public function getVolunteer(): ?User
    {
        return $this->volunteer;
    }
    public function setVolunteer(?User $volunteer)
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

    public function getFormattedDateCreated()
    {
        return $this->dateCreated->format('d/m/y');
    }

    public function setDateCreated($dateCreated)
    {
        $this->dateCreated = $dateCreated;
    }

    public function getDateAssigned()
    {
        return $this->dateAssigned;
    }

    public function getFormattedDateAssigned()
    {
        return $this->dateAssigned->format('d/m/y');
    }

    public function setDateAssigned($dateAssigned)
    {
        $this->dateAssigned = $dateAssigned;
    }

    public function getDateFinished()
    {
        return $this->dateFinished;
    }

    public function getFormattedDateFinished()
    {
        return $this->dateFinished->format('d/m/y');
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
