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
     */
    private $gla;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     *
     * @var User
     *
     * @Assert\NotNull()
     */
    private $referent;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     *
     * @var User
     *
     * @Assert\NotNull()
     */
    private $inspector;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Address")
     * @ORM\JoinColumn(nullable=false)
     *
     * @var Address
     *
     * @Assert\NotNull()
     */
    private $address;

    /**
     * @ORM\Column(type="datetime")
     *
     * @var \DateTime
     */
    private $created;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\BuildingInspectionItem", mappedBy="inspection", orphanRemoval=true)
     */
    private $items;

    /**
     * BuildingInspection constructor.
     *
     * @throws \Exception
     */
    public function __construct()
    {
        $this->items = new ArrayCollection();
        $this->created = new \DateTime();
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
    public function getCreated(): \DateTime
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
}
