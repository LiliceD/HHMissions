<?php

namespace App\Model;

use App\Entity\BuildingInspectionItem;
use App\Entity\BuildingInspectionItemHeaders;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class BuildingInspectionItemDisplay
 *
 * @author Alice Dahan <lilice.dhn@gmail.com>
 */
class BuildingInspectionItemDisplay
{
    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Length(max="50")
     */
    private $name;

    /**
     * @var string
     *
     * @Assert\Choice(callback={"getThemes"})
     */
    private $theme;

    /**
     * @var string
     *
     * @Assert\Length(max="255")
     */
    private $description;

    /**
     * @var int
     */
    private $rank;

    /**
     * @var string
     *
     * @Assert\Length(max="255")
     */
    private $comment;

    /**
     * @var string
     *
     * @Assert\Choice(callback="getActions")
     */
    private $action;

    /**
     * @var string
     *
     * @Assert\Choice(callback="getDecisionMaker")
     * @Assert\Expression(
     *     "value or !this.getAction()",
     *     message="Une action nécessite de choisir des décisionnaires.",
     * )
     */
    private $decisionMaker;

    /**
     * BuildingInspectionItemDisplay constructor.
     *
     * @param BuildingInspectionItemHeaders $headers
     * @param BuildingInspectionItem|null   $item
     */
    public function __construct(BuildingInspectionItemHeaders $headers, BuildingInspectionItem $item = null)
    {
        $this->name = $headers->getName();
        $this->theme = $headers->getTheme();
        $this->description = $headers->getDescription();
        $this->rank = $headers->getRank();

        if ($item instanceof BuildingInspectionItem) {
            $this->comment = $item->getComment();
            $this->action = $item->getAction();
            $this->decisionMaker = $item->getDecisionMaker();
        }
    }

    /**
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return BuildingInspectionItemDisplay
     */
    public function setName(string $name): BuildingInspectionItemDisplay
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getTheme(): ?string
    {
        return $this->theme;
    }

    /**
     * @param string $theme
     *
     * @return BuildingInspectionItemDisplay
     */
    public function setTheme(string $theme): BuildingInspectionItemDisplay
    {
        $this->theme = $theme;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return BuildingInspectionItemDisplay
     */
    public function setDescription(string $description): BuildingInspectionItemDisplay
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return int
     */
    public function getRank(): ?int
    {
        return $this->rank;
    }

    /**
     * @param int $rank
     *
     * @return BuildingInspectionItemDisplay
     */
    public function setRank(int $rank): BuildingInspectionItemDisplay
    {
        $this->rank = $rank;

        return $this;
    }

    /**
     * @return string
     */
    public function getComment(): ?string
    {
        return $this->comment;
    }

    /**
     * @param string $comment
     *
     * @return BuildingInspectionItemDisplay
     */
    public function setComment(string $comment): BuildingInspectionItemDisplay
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * @return string
     */
    public function getAction(): ?string
    {
        return $this->action;
    }

    /**
     * @param string $action
     *
     * @return BuildingInspectionItemDisplay
     */
    public function setAction(string $action): BuildingInspectionItemDisplay
    {
        $this->action = $action;

        return $this;
    }

    /**
     * @return string
     */
    public function getDecisionMaker(): ?string
    {
        return $this->decisionMaker;
    }

    /**
     * @param string $decisionMaker
     *
     * @return BuildingInspectionItemDisplay
     */
    public function setDecisionMaker(string $decisionMaker): BuildingInspectionItemDisplay
    {
        $this->decisionMaker = $decisionMaker;

        return $this;
    }
}
