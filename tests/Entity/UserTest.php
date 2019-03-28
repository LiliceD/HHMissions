<?php

namespace App\Tests\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \App\Entity\User
 *
 * @author Alice Dahan <lilice.dhn@gmail.com>
 */
class UserTest extends TestCase
{
    /**
     * @covers ::getInitials
     *
     * @dataProvider getInitialsProvider
     *
     * @param User|null $user
     * @param string    $expectedResult
     */
    public function testGetInitials(?User $user, string $expectedResult): void
    {
        $this->assertEquals($expectedResult, $user->getInitials());
    }

    public function getInitialsProvider(): array
    {
        $user = new User();
        $user->setName('coucou toi');

        $multipleNamesUser = new User();
        $multipleNamesUser->setName('marie-joe de la fontaine');

        return [
            [new User(), ''],
            [$user, 'CT'],
            [$multipleNamesUser, 'MJDLF'],
        ];
    }
}
