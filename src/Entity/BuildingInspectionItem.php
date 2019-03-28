<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class BuildingInspectionItem
 *
 * @ORM\Entity(repositoryClass="App\Repository\BuildingInspectionItemRepository")
 *
 * @author Alice Dahan <lilice.dhn@gmail.com>
 */
class BuildingInspectionItem
{
    public const LABEL_COMMENT = "Constats faits par le visiteur / Propositions d'actions";
    public const LABEL_ACTION = "Action";
    public const LABEL_DECISION_MAKER = "Décisions d'engagement";

    public const ACTION_URGENT = 'Urgente';
    public const ACTION_FOLLOW = 'A suivre';
    public const ACTION_REMEMBER = 'A retenir';

    private const DECISION_GLA = 'GLA';
    private const DECISION_DIY = 'Bricoleurs';
    private const DECISION_VOLUNTEER = 'Bénévoles GLA (Mission)';

    /**
     * Get allowed actions for BuildingInspectionItem
     *
     * @return array
     */
    public static function getActions(): array
    {
        return [
            '' => null,
            self::ACTION_URGENT => self::ACTION_URGENT,
            self::ACTION_FOLLOW => self::ACTION_FOLLOW,
            self::ACTION_REMEMBER => self::ACTION_REMEMBER,
        ];
    }

    /**
     * Get allowed decision makers for BuildingInspectionItem
     *
     * @return array
     */
    public static function getDecisionMakers(): array
    {
        return [
            '' => null,
            self::DECISION_GLA => self::DECISION_GLA,
            self::DECISION_DIY => self::DECISION_DIY,
            self::DECISION_VOLUNTEER => self::DECISION_VOLUNTEER,
        ];
    }

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     *
     * @var int
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\BuildingInspectionItemHeaders")
     * @ORM\JoinColumn(nullable=false)
     *
     * @var BuildingInspectionItemHeaders
     *
     * @Assert\NotNull()
     */
    private $headers;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     * @var string
     *
     * @Assert\Length(max="1000")
     */
    private $comment;

    /**
     * @ORM\Column(type="string", length=25, nullable=true)
     *
     * @var string
     *
     * @Assert\Choice(callback="getActions")
     */
    private $action;

    /**
     * @ORM\Column(type="string", length=25, nullable=true)
     *
     * @var string
     *
     * @Assert\Choice(callback="getDecisionMakers")
     * @Assert\Expression(
     *     "value or !this.getAction()",
     *     message="Une action nécessite de choisir des décisionnaires.",
     * )
     */
    private $decisionMaker;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\BuildingInspection", inversedBy="items")
     * @ORM\JoinColumn(nullable=false)
     *
     * @var BuildingInspection
     *
     * @Assert\NotNull()
     */
    private $inspection;

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
     * @return BuildingInspectionItem
     */
    public function setId(int $id): BuildingInspectionItem
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return BuildingInspectionItemHeaders
     */
    public function getHeaders(): ?BuildingInspectionItemHeaders
    {
        return $this->headers;
    }

    /**
     * @param BuildingInspectionItemHeaders $headers
     *
     * @return BuildingInspectionItem
     */
    public function setHeaders(BuildingInspectionItemHeaders $headers): BuildingInspectionItem
    {
        $this->headers = $headers;

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
     * @return BuildingInspectionItem
     */
    public function setComment(string $comment): BuildingInspectionItem
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
     * @return BuildingInspectionItem
     */
    public function setAction(string $action): BuildingInspectionItem
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
     * @return BuildingInspectionItem
     */
    public function setDecisionMaker(string $decisionMaker): BuildingInspectionItem
    {
        $this->decisionMaker = $decisionMaker;

        return $this;
    }

    /**
     * @return BuildingInspection
     */
    public function getInspection(): ?BuildingInspection
    {
        return $this->inspection;
    }

    /**
     * @param BuildingInspection $inspection
     *
     * @return BuildingInspectionItem
     */
    public function setInspection(?BuildingInspection $inspection): BuildingInspectionItem
    {
        $this->inspection = $inspection;

        return $this;
    }
}
