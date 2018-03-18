<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MissionControllerTest extends WebTestCase
{
    public static function setUpBeforeClass()
    {
        shell_exec('php bin/console doctrine:database:import habhum-tests-drop.sql');
        shell_exec('php bin/console doctrine:database:import habhum-tests-import.sql');
    }

    
    /**
     * @group url
     *
     * @dataProvider missionUrls
     */
    public function testMissionUrls($url)
    {
        $client = static::createClient();

        $client->request('GET', $url);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function missionUrls()
    {
        return [
            ['/missions'],
            ['/missions/add'],
            ['/missions/view/1'],
            ['/missions/edit/1'],
            ['/missions/recap']
        ];
    }


    /**
     * @group mission
     */
    public function testMissionsList()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/missions');

        // 4 missions in initial test db
        $this->assertSame(4, $crawler->filter('tbody > tr')->count());
    }

    /**
     * @group mission
     */
    public function testAddMission()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/missions/add');

        $form = $crawler->selectButton('Créer la Fiche Mission')->form();

        $form['mission[gla]']->select('Christian ROBAYE');
        $form['mission[accomodation]']->select('8');    // 35-37 RUE JULES BRUNARD 69007 LYON 7
        $form['mission[description]'] = 'Test description de nouvelle mission';

        $crawler = $client->submit($form);
        $crawler = $client->followRedirect();

        $currentDate = date('d/m/Y');

        // New mission id is 5 because of test database initialization
        $this->assertSame(1, $crawler->filter('h3:contains("Fiche mission GLA-Bénévole n°5")')->count());   
        $this->assertSame(1, $crawler->filter('#status:contains("Créée")')->count());
        $this->assertSame(1, $crawler->filter('#dateCreated:contains("'.$currentDate.'")')->count());
        $this->assertSame(1, $crawler->filter('#gla:contains("Christian ROBAYE")')->count());
        $this->assertSame(1, $crawler->filter('#volunteer:contains("")')->count());
        $this->assertSame(1, $crawler->filter('#address:contains("35-37 RUE JULES BRUNARD 69007 LYON 7")')->count());
        $this->assertSame(1, $crawler->filter('#description:contains("Test description de nouvelle mission")')->count());
    }

    /**
     * Access view of the mission created in testAddMission() from /missions
     *
     * @group mission
     */
    public function testViewMissionFromList()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/missions');

        $link = $crawler->filter('#mission-5')->selectLink('Voir')->link();

        $crawler = $client->click($link);

        $this->assertSame(1, $crawler->filter('h3:contains("Fiche mission GLA-Bénévole n°5")')->count());
    }

    /**
     * Access edit of the mission created in testAddMission() from /missions
     *
     * @group mission
     */
    public function testEditMissionFromList()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/missions');

        $link = $crawler->filter('#mission-5')->selectLink('Modifier')->link();

        $crawler = $client->click($link);

        $this->assertSame(1, $crawler->filter('h3:contains("Modifier la fiche mission n°5")')->count());
    }

    /**
     * Edit the mission created in testAddMission()
     *
     * @group mission
     */
    public function testEditMission()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/missions/edit/5');

        $form = $crawler->selectButton('Valider')->form();

        $form['mission[volunteer]']->select('Bernard BODIN');
        $form['mission[dateAssigned]'] = '2018-03-14';
        $form['mission[info]'] = 'Test infos complémentaires modifiées';

        $crawler = $client->submit($form);
        $crawler = $client->followRedirect();

        $this->assertSame(1, $crawler->filter('h3:contains("Fiche mission GLA-Bénévole n°5")')->count());
        $this->assertSame(1, $crawler->filter('#status:contains("Prise en charge")')->count());
        $this->assertSame(1, $crawler->filter('#dateAssigned:contains("14/03/2018")')->count());
        $this->assertSame(1, $crawler->filter('#volunteer:contains("Bernard BODIN")')->count());
        $this->assertSame(1, $crawler->filter('#info:contains("Test infos complémentaires modifiées")')->count());
    }

    /**
     * Delete the mission created in testAddMission()
     *
     * @group mission
     */
    public function testDeleteMission()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/missions/view/5');

        $link = $crawler->selectLink('Supprimer')->link();

        $crawler = $client->click($link);
        $crawler = $client->followRedirect();

        $this->assertSame(1, $crawler->filter('h3:contains("Liste des missions")')->count());
        $this->assertSame(1, $crawler->filter('.alert-success:contains("La fiche mission a bien été supprimée")')->count());
        $this->assertSame(0, $crawler->filter('#mission-5')->count());
    }


    /**
     * @group mission
     */
    public function testReopenClosedMission()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/missions/view/1');

        $link = $crawler->selectLink('Rouvrir')->link();

        $crawler = $client->click($link);
        $crawler = $client->followRedirect();

        $this->assertSame(1, $crawler->filter('h3:contains("Fiche mission GLA-Bénévole n°1")')->count());
        $this->assertSame(1, $crawler->filter('.alert-success:contains("La fiche mission a bien été ré-ouverte")')->count());
        $this->assertSame(1, $crawler->filter('#status:contains("Terminée")')->count());
    }

    /**
     * @group mission
     */
    public function testCloseFinishedMission()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/missions/view/1');

        $link = $crawler->selectLink('Fermer')->link();

        $crawler = $client->click($link);
        $crawler = $client->followRedirect();

        $this->assertSame(1, $crawler->filter('h3:contains("Fiche mission GLA-Bénévole n°1")')->count());
        $this->assertSame(1, $crawler->filter('.alert-success:contains("La fiche mission a bien été fermée")')->count());
        $this->assertSame(1, $crawler->filter('#status:contains("Fermée")')->count());
    }
}
