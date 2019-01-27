<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class BuildingInspectionItemHeaders
 *
 * @ORM\Entity(repositoryClass="App\Repository\BuildingInspectionItemHeadersRepository")
 *
 * @author Alice Dahan <lilice.dhn@gmail.com>
 */
class BuildingInspectionItemHeaders
{
    private const THEME_1 = '1_Sécurité';
    private const THEME_2 = '2_Hygiène_Propreté';
    private const THEME_3 = '3_Etat général';
    private const THEME_4 = '4_Equipements techniques';
    private const THEME_5 = '5_Divers';
    private const THEME_6 = '6_REX Capitalisation';

    /**
     * Get allowed themes for BuildingInspectionItemHeaders
     *
     * @return array
     */
    public static function getThemes(): array
    {
        return [
            self::THEME_1 => self::THEME_1,
            self::THEME_2 => self::THEME_2,
            self::THEME_3 => self::THEME_3,
            self::THEME_4 => self::THEME_4,
            self::THEME_5 => self::THEME_5,
            self::THEME_6 => self::THEME_6,
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
     * @ORM\Column(type="string", length=50)
     *
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Length(max="50")
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=30)
     *
     * @var string
     *
     * @Assert\Choice(callback={"getThemes"})
     */
    private $theme;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @var string
     *
     * @Assert\Length(max="255")
     */
    private $description;

    /**
     * @ORM\Column(type="integer")
     *
     * @var int
     */
    private $rank = 1;

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
     * @return BuildingInspectionItemHeaders
     */
    public function setId(int $id): BuildingInspectionItemHeaders
    {
        $this->id = $id;

        return $this;
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
     * @return BuildingInspectionItemHeaders
     */
    public function setName(string $name): BuildingInspectionItemHeaders
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
     * @return BuildingInspectionItemHeaders
     */
    public function setTheme(string $theme): BuildingInspectionItemHeaders
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
     * @return BuildingInspectionItemHeaders
     */
    public function setDescription(string $description): BuildingInspectionItemHeaders
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return int
     */
    public function getRank(): int
    {
        return $this->rank;
    }

    /**
     * @param int $rank
     *
     * @return BuildingInspectionItemHeaders
     */
    public function setRank(int $rank): BuildingInspectionItemHeaders
    {
        $this->rank = $rank;

        return $this;
    }
}
