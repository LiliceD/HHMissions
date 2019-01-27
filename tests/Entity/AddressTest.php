<?php

namespace App\Tests\Entity;

use App\Entity\Address;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \App\Entity\Address
 *
 * @author Alice Dahan <lilice.dhn@gmail.com>
 */
class AddressTest extends TestCase
{
    /**
     * @covers ::isBuilding
     *
     * @dataProvider isBuildingProvider
     *
     * @param Address $address
     * @param bool $expectedResult
     */
    public function testIsBuilding(Address $address, bool $expectedResult): void
    {
        $this->assertEquals($expectedResult, $address->isBuilding());
    }

    /**
     * @return array
     */
    public function isBuildingProvider(): array
    {
        $buildingAddress1 = new Address();
        $buildingAddress1->setOwnerType('FHH - Immeuble');

        $buildingAddress2= new Address();
        $buildingAddress2->setOwnerType('Privé - Immeuble');

        $notBuildingAddress1 = new Address();
        $notBuildingAddress1->setOwnerType('FHH - Logement isolé');

        $notBuildingAddress2 = new Address();
        $notBuildingAddress2->setOwnerType('Privé - Logement isolé');

        $dummyAddress = new Address();
        $dummyAddress->setOwnerType('dummy');

        $emptyAddress = new Address();

        return [
            [$buildingAddress1, true],
            [$buildingAddress2, true],
            [$notBuildingAddress1, false],
            [$notBuildingAddress2, false],
            [$dummyAddress, false],
            [$emptyAddress, false],
        ];
    }
}
