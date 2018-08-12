<?php

namespace App\Entity;

use App\Utils\Constant;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @ORM\Table(name="mission")
 *
 * @ORM\Entity(repositoryClass="App\Repository\MissionRepository")
 */
class Mission
{
    //  █████╗ ████████╗████████╗██████╗ ██╗██████╗ ██╗   ██╗████████╗███████╗███████╗
    // ██╔══██╗╚══██╔══╝╚══██╔══╝██╔══██╗██║██╔══██╗██║   ██║╚══██╔══╝██╔════╝██╔════╝
    // ███████║   ██║      ██║   ██████╔╝██║██████╔╝██║   ██║   ██║   █████╗  ███████╗
    // ██╔══██║   ██║      ██║   ██╔══██╗██║██╔══██╗██║   ██║   ██║   ██╔══╝  ╚════██║
    // ██║  ██║   ██║      ██║   ██║  ██║██║██████╔╝╚██████╔╝   ██║   ███████╗███████║
    // ╚═╝  ╚═╝   ╚═╝      ╚═╝   ╚═╝  ╚═╝╚═╝╚═════╝  ╚═════╝    ╚═╝   ╚══════╝╚══════╝

    /**
     * Mission's id
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     *
     * @var int
     */
    protected $id;

    /**
     * Mission's status (default, assigned, finished or closed)
     *
     * @ORM\Column(type="string", length=25)
     *
     * @Assert\NotBlank()
     * @Assert\Choice(callback={"App\Utils\Constant", "getStatuses"})
     *
     * @var string
     */
    protected $status;

    /**
     * Mission's Address of intervention
     *
     * @ORM\ManyToOne(
     *     targetEntity="Address",
     *     inversedBy="missions",
     * )
     * @ORM\JoinColumn(nullable=false)
     *
     * @Assert\NotBlank()
     *
     * @var Address
     */
    protected $address;

    /**
     * User who created the Mission
     *
     * @ORM\ManyToOne(
     *     targetEntity="App\Entity\User",
     *     inversedBy="missionsAsGla",
     * )
     * @ORM\JoinColumn(nullable=false)
     *
     * @Assert\NotBlank()
     * @Assert\Expression(
     *     "value.isGla() and value.isActive()",
     *     message="Cette personne n'est pas dans la catégorie des GLA actifs.",
     * )
     *
     * @var User
     */
    protected $gla;

    /**
     * User (volunteer) assigned to the mission
     *
     * @ORM\ManyToOne(
     *     targetEntity="App\Entity\User",
     *     inversedBy="missionsAsVolunteer",
     * )
     * @ORM\JoinColumn(nullable=true)
     *
     * @Assert\Expression(
     *     "!value or (value.isVolunteer() and value.isActive())",
     *     message="Cette personne n'est pas dans la catégorie des bénévoles actifs.",
     * )
     *
     * @var User
     */
    protected $volunteer;

    /**
     * Mission's pole of activity (the type of the mission)
     *
     * @ORM\Column(type="string", length=50, nullable=true)
     *
     * @Assert\Choice(callback={"App\Utils\Constant", "getActivities"})
     *
     * @var string
     */
    protected $activity;

    /**
     * Mission's description (what it consists of)
     *
     * @ORM\Column(type="string", length=3000)
     *
     * @Assert\NotBlank(message="Veuillez remplir la description de la mission")
     * @Assert\Length(max=3000)
     *
     * @var string
     */
    protected $description;

    /**
     * Mission's information (e.g. access details for the address)
     *
     * @ORM\Column(type="string", nullable=true, length=1000)
     *
     * @Assert\Length(max=1000)
     *
     * @var string
     */
    protected $info;

    /**
     * Mission's conclusions from the volunteer
     *
     * @ORM\Column(type="string", nullable=true, length=3000)
     *
     * @Assert\Length(max=3000)
     *
     * @var string
     */
    protected $conclusions;

    /**
     * Mission's date of creation
     *
     * @ORM\Column(name="date_created", type="date")
     *
     * @Assert\NotBlank()
     * @Assert\LessThanOrEqual(
     *     "today",
     *     message="La date de demande ne peut pas être dans le futur.",
     * )
     *
     * @var \DateTime
     */
    protected $dateCreated;

    /**
     * Mission's date of assignment
     *
     * @ORM\Column(name="date_assigned", type="date", nullable=true)
     *
     * @Assert\LessThanOrEqual("today", message="La date de prise en charge ne peut pas être dans le futur.")
     * @Assert\Expression(
     *     "!value or value >= this.getDateCreated()",
     *     message="La date de prise en charge doit être après la date de demande.",
     * )
     *
     * @var \DateTime
     */
    protected $dateAssigned;

    /**
     * Mission's date of end (when the volunteer has finished)
     *
     * @ORM\Column(name="date_finished", type="date", nullable=true)
     *
     * @Assert\LessThanOrEqual("today", message="La date de fin de mission ne peut pas être dans le futur.")
     * @Assert\Expression(
     *     "!value or value >= this.getDateAssigned()",
     *     message="La date de fin de mission doit être après la date de prise en charge."
     * )
     *
     * @var \DateTime
     */
    protected $dateFinished;

    /**
     * Mission's attachment (a pdf file)
     *
     * @ORM\Column(type="string", nullable=true)
     *
     * @Assert\File(mimeTypes={ "application/pdf" })
     *
     * @var string
     */
    protected $attachment;

    /**
     * Mission's duration (minutes of volunteering)
     *
     * @ORM\Column(type="integer", nullable=true)
     *
     * @var int
     */
    protected $duration;

    /**
     * Mission's distance (in kms) covered by the volunteer
     *
     * @ORM\Column(type="integer", nullable=true)
     *
     * @var int
     */
    protected $distance;

    // ███╗   ███╗███████╗████████╗██╗  ██╗ ██████╗ ██████╗ ███████╗
    // ████╗ ████║██╔════╝╚══██╔══╝██║  ██║██╔═══██╗██╔══██╗██╔════╝
    // ██╔████╔██║█████╗     ██║   ███████║██║   ██║██║  ██║███████╗
    // ██║╚██╔╝██║██╔══╝     ██║   ██╔══██║██║   ██║██║  ██║╚════██║
    // ██║ ╚═╝ ██║███████╗   ██║   ██║  ██║╚██████╔╝██████╔╝███████║
    // ╚═╝     ╚═╝╚══════╝   ╚═╝   ╚═╝  ╚═╝ ╚═════╝ ╚═════╝ ╚══════╝

    /**
     * Entity validation function
     *
     * @Assert\Callback
     *
     * @param ExecutionContextInterface $context
     */
    public function validate(ExecutionContextInterface $context)
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

        // A mission with a dateFinished must have a dateAssigned first
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

        // A mission's volunteer must be in the same pole of activity
        if ($this->getVolunteer() instanceof User
           && !in_array($this->getActivity(), $this->getVolunteer()->getActivities())) {
            $context
                ->buildViolation(
                    'Une mission ne peut pas être prise en charge par un·e bénévole d\'un autre pôle d\'activité.'
                )
                ->atPath('volunteer')
                ->addViolation();
        }
    }

    /**
     * Mission constructor.
     */
    public function __construct()
    {
        $this->status = Constant::getStatuses()[Constant::STATUS_DEFAULT];
        $this->dateCreated = new \DateTime();
    }

    //  █████╗  ██████╗ ██████╗███████╗███████╗███████╗ ██████╗ ██████╗ ███████╗
    // ██╔══██╗██╔════╝██╔════╝██╔════╝██╔════╝██╔════╝██╔═══██╗██╔══██╗██╔════╝
    // ███████║██║     ██║     █████╗  ███████╗███████╗██║   ██║██████╔╝███████╗
    // ██╔══██║██║     ██║     ██╔══╝  ╚════██║╚════██║██║   ██║██╔══██╗╚════██║
    // ██║  ██║╚██████╗╚██████╗███████╗███████║███████║╚██████╔╝██║  ██║███████║
    // ╚═╝  ╚═╝ ╚═════╝ ╚═════╝╚══════╝╚══════╝╚══════╝ ╚═════╝ ╚═╝  ╚═╝╚══════╝

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus(string $status)
    {
        $this->status = $status;
    }

    /**
     * @return Address|null
     */
    public function getAddress(): ?Address
    {
        return $this->address;
    }

    /**
     * @param Address $address
     */
    public function setAddress(Address $address)
    {
        $this->address = $address;
    }

    /**
     * @return User|null
     */
    public function getGla(): ?User
    {
        return $this->gla;
    }

    /**
     * @param User $gla
     */
    public function setGla(User $gla)
    {
        $this->gla = $gla;
    }

    /**
     * @return User|null
     */
    public function getVolunteer(): ?User
    {
        return $this->volunteer;
    }

    /**
     * @param User|null $volunteer
     */
    public function setVolunteer(?User $volunteer)
    {
        $this->volunteer = $volunteer;
    }

    /**
     * @return string|null
     */
    public function getActivity(): ?string
    {
        return $this->activity;
    }

    /**
     * @param string $activity
     */
    public function setActivity(string $activity)
    {
        $this->activity = $activity;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description)
    {
        $this->description = $description;
    }

    /**
     * @return string|null
     */
    public function getInfo(): ?string
    {
        return $this->info;
    }

    /**
     * @param string $info
     */
    public function setInfo(string $info)
    {
        $this->info = $info;
    }

    /**
     * @return string|null
     */
    public function getConclusions(): ?string
    {
        return $this->conclusions;
    }

    /**
     * @param string $conclusions
     */
    public function setConclusions(string $conclusions)
    {
        $this->conclusions = $conclusions;
    }

    /**
     * @return \DateTime
     */
    public function getDateCreated(): \DateTime
    {
        return $this->dateCreated;
    }

    /**
     * @return string
     */
    public function getFormattedDateCreated(): string
    {
        return $this->dateCreated->format('d/m/y');
    }

    /**
     * @param \DateTime $dateCreated
     */
    public function setDateCreated(\DateTime $dateCreated)
    {
        $this->dateCreated = $dateCreated;
    }

    /**
     * @return \DateTime|null
     */
    public function getDateAssigned(): ?\DateTime
    {
        return $this->dateAssigned;
    }

    /**
     * @return string|null
     */
    public function getFormattedDateAssigned(): ?string
    {
        if ($this->dateAssigned instanceof \DateTime) {
            return $this->dateAssigned->format('d/m/y');
        }

        return null;
    }

    /**
     * @param \DateTime|null $dateAssigned
     */
    public function setDateAssigned(?\DateTime $dateAssigned)
    {
        $this->dateAssigned = $dateAssigned;
    }

    /**
     * @return \DateTime|null
     */
    public function getDateFinished(): ?\DateTime
    {
        return $this->dateFinished;
    }

    /**
     * @return string|null
     */
    public function getFormattedDateFinished(): ?string
    {
        if ($this->dateFinished instanceof \DateTime) {
            return $this->dateFinished->format('d/m/y');
        }

        return null;
    }

    /**
     * @param \DateTime|null $dateFinished
     */
    public function setDateFinished(?\DateTime $dateFinished)
    {
        $this->dateFinished = $dateFinished;
    }

    /**
     * @return UploadedFile|string|null
     */
    public function getAttachment()
    {
        return $this->attachment;
    }

    /**
     * @param File|string|null $attachment
     */
    public function setAttachment($attachment)
    {
        $this->attachment = $attachment;
    }

    /**
     * @return int|null
     */
    public function getDuration(): ?int
    {
        return $this->duration;
    }

    /**
     * @param string $duration
     */
    public function setDuration(string $duration)
    {
        $this->duration = $duration;
    }

    /**
     * @return int|null
     */
    public function getDistance(): ?int
    {
        return $this->distance;
    }

    /**
     * @param string $distance
     */
    public function setDistance(string $distance)
    {
        $this->distance = $distance;
    }
}
