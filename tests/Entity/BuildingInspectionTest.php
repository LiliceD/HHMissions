<?php

namespace App\Tests\Entity;

use App\Entity\BuildingInspection;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \App\Entity\BuildingInspection
 *
 * @author Alice Dahan <lilice.dhn@gmail.com>
 */
class BuildingInspectionTest extends TestCase
{
    /** @var BuildingInspection */
    private $buildingInspection;

    /**
     * @throws \Exception
     */
    public function setUp(): void
    {
        $this->buildingInspection = new BuildingInspection();
    }

    /**
     * @covers ::getReferentInitials
     *
     * @dataProvider getReferentInitialsProvider
     *
     * @param User|null $referent
     * @param string $expectedResult
     */
    public function testGetReferentInitials(?User $referent, string $expectedResult): void
    {
        if (!is_null($referent)) {
            $this->buildingInspection->setReferent($referent);
        }

        $this->assertEquals($expectedResult, $this->buildingInspection->getReferentInitials());
    }

    public function getReferentInitialsProvider(): array
    {
        $referent = new User();
        $referent->setName('coucou toi');

        $multipleNamesReferent = new User();
        $multipleNamesReferent->setName('marie-joe de la fontaine');

        return [
            [null, ''],
            [$referent, 'CT'],
            [$multipleNamesReferent, 'MJDLF'],
        ];
    }
}
