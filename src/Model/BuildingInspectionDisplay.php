<?php

namespace App\Model;

use App\Entity\Address;
use App\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class BuildingInspectionDisplay
 *
 * @author Alice Dahan <lilice.dhn@gmail.com>
 */
class BuildingInspectionDisplay
{
    /**
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
     * @var \DateTime
     */
    private $created;

    /**
     * @var BuildingInspectionItemDisplay[]
     *
     * @Assert\Valid()
     */
    private $items = [];

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
     * @return BuildingInspectionDisplay
     */
    public function setGla(User $gla): BuildingInspectionDisplay
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
     * @return BuildingInspectionDisplay
     */
    public function setReferent(User $referent): BuildingInspectionDisplay
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
     * @return BuildingInspectionDisplay
     */
    public function setInspector(User $inspector): BuildingInspectionDisplay
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
     * @return BuildingInspectionDisplay
     */
    public function setAddress(Address $address): BuildingInspectionDisplay
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
     * @return BuildingInspectionDisplay
     */
    public function setCreated(\DateTime $created): BuildingInspectionDisplay
    {
        $this->created = $created;

        return $this;
    }

    /**
     * @return BuildingInspectionItemDisplay[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @param BuildingInspectionItemDisplay[] $items
     *
     * @return BuildingInspectionDisplay
     */
    public function setItems(array $items): BuildingInspectionDisplay
    {
        $this->items = $items;

        return $this;
    }
}
