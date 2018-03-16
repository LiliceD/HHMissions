<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MissionControllerTest extends WebTestCase
{
	public function testMissionsList()
    {
        $client = static::createClient();

        $client->request('GET', 'http://localhost:8000/missions');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}