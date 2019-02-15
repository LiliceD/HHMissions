<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class BuildingInspection
 *
 * @ORM\Entity(repositoryClass="App\Repository\BuildingInspectionRepository")
 *
 * @author Alice Dahan <lilice.dhn@gmail.com>
 */
class BuildingInspection
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     *
     * @var int
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     *
     * @var User
     *
     * @Assert\NotNull()
     * @Assert\Expression(
     *     "value.isGla() and value.isActive()",
     *     message="Cette personne n'est pas dans la catégorie des GLA actifs.",
     * )
     */
    private $gla;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     *
     * @var User
     *
     * @Assert\NotNull()
     * @Assert\Expression(
     *     "value.isVolunteer() and value.isActive()",
     *     message="Cette personne n'est pas dans la catégorie des bénévoles actifs.",
     * )
     */
    private $referent;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     *
     * @var User
     *
     * @Assert\NotNull()
     * @Assert\Expression(
     *     "value.isVolunteer() and value.isActive()",
     *     message="Cette personne n'est pas dans la catégorie des bénévoles actifs.",
     * )
     */
    private $inspector;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Address")
     * @ORM\JoinColumn(nullable=false)
     *
     * @var Address
     *
     * @Assert\NotNull()
     * @Assert\Expression(
     *     "value.isBuilding()",
     *     message="Ce logement n'est pas un immeuble.",
     * )
     */
    private $address;

    /**
     * @ORM\Column(type="datetime")
     *
     * @var \DateTime
     *
     * @Assert\NotNull()
     */
    private $created;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\BuildingInspectionItem", mappedBy="inspection", orphanRemoval=true)
     *
     * @var ArrayCollection|Collection|array
     *
     * @Assert\Valid()
     */
    private $items;

    /**
     * BuildingInspection constructor.
     *
     * @throws \Exception
     */
    public function __construct()
    {
        $this->created = new \DateTime();
        $this->items = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return BuildingInspection
     */
    public function setId(int $id): BuildingInspection
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return User
     */
    public function getGla(): ?User
    {
        return $this->gla;
    }

    /**
     * @param User $gla
     *
     * @return BuildingInspection
     */
    public function setGla(User $gla): BuildingInspection
    {
        $this->gla = $gla;

        return $this;
    }

    /**
     * @return User
     */
    public function getReferent(): ?User
    {
        return $this->referent;
    }

    /**
     * @return string
     */
    public function getReferentInitials(): string
    {
        $initials = '';

        if ($this->referent instanceof User) {
            $nameWithoutHyphens = str_replace('-', ' ', $this->referent->getName());
            $nameAsArray = explode(' ', $nameWithoutHyphens);
            $initialsAsArray = (new ArrayCollection($nameAsArray))
                ->map(function ($word): string {
                    return strtoupper(substr($word, 0, 1));
                })
                ->getValues();
            $initials = implode('', $initialsAsArray);
        }

        return $initials;
    }

    /**
     * @param User $referent
     *
     * @return BuildingInspection
     */
    public function setReferent(User $referent): BuildingInspection
    {
        $this->referent = $referent;

        return $this;
    }

    /**
     * @return User
     */
    public function getInspector(): ?User
    {
        return $this->inspector;
    }

    /**
     * @param User $inspector
     *
     * @return BuildingInspection
     */
    public function setInspector(User $inspector): BuildingInspection
    {
        $this->inspector = $inspector;

        return $this;
    }

    /**
     * @return Address
     */
    public function getAddress(): ?Address
    {
        return $this->address;
    }

    /**
     * @param Address $address
     *
     * @return BuildingInspection
     */
    public function setAddress(Address $address): BuildingInspection
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreated(): ?\DateTime
    {
        return $this->created;
    }

    /**
     * @param \DateTime $created
     *
     * @return BuildingInspection
     */
    public function setCreated(\DateTime $created): BuildingInspection
    {
        $this->created = $created;

        return $this;
    }

    /**
     * @return Collection|BuildingInspectionItem[]
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    /**
     * @param BuildingInspectionItem $item
     *
     * @return BuildingInspection
     */
    public function addItem(BuildingInspectionItem $item): BuildingInspection
    {
        if (!$this->items->contains($item)) {
            $this->items[] = $item;
            $item->setInspection($this);
        }

        return $this;
    }

    /**
     * @param BuildingInspectionItem $item
     *
     * @return BuildingInspection
     */
    public function removeItem(BuildingInspectionItem $item): BuildingInspection
    {
        if ($this->items->contains($item)) {
            $this->items->removeElement($item);
            if ($item->getInspection() === $this) {
                $item->setInspection(null);
            }
        }

        return $this;
    }
}
