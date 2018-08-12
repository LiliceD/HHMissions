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

    private $clientA; // client with ROLE_ADMIN
    private $clientG; // client with ROLE_GLA
    private $clientV; // client with ROLE_VOLUNTEER

    public function setUp()
    {
        // a.test is a dummy user with ROLE_ADMIN
        $this->clientA = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'a.test',
            'PHP_AUTH_PW'   => 'a.test',
        ));

        // g.test is a dummy user with ROLE_GLA
        $this->clientG = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'g.test',
            'PHP_AUTH_PW'   => 'g.test',
        ));

        // v.test is a dummy user with ROLE_VOLUNTEER
        $this->clientV = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'v.test',
            'PHP_AUTH_PW'   => 'v.test',
        ));
    }

    
    /**
     * @group url
     *
     * @dataProvider missionUrls
     */
    public function testMissionUrls($url, $role, $code)
    {
        if ($role === 'ROLE_ADMIN') {
            $client = $this->clientA;    
        } else if ($role === 'ROLE_GLA') {
            $client = $this->clientG;    
        } else if ($role === 'ROLE_VOLUNTEER') {
            $client = $this->clientV;    
        }

        $client->request('GET', $url);

        $this->assertEquals($code, $client->getResponse()->getStatusCode());
    }

    public function missionUrls()
    {
        return [
            ['/missions', 'ROLE_GLA', 200],
            ['/missions', 'ROLE_VOLUNTEER', 200],
            ['/missions/recap', 'ROLE_GLA', 200],
            ['/missions/recap', 'ROLE_VOLUNTEER', 200],
            ['/missions/voir/1', 'ROLE_GLA', 200],
            ['/missions/voir/1', 'ROLE_VOLUNTEER', 200],
            
            ['/missions/modifier/2', 'ROLE_GLA', 200], // Gla TEST has created mission 2
            ['/missions/modifier/2', 'ROLE_VOLUNTEER', 200], // Volunteer TEST is assigned to mission 2
            ['/missions/modifier/3', 'ROLE_ADMIN', 200],
            ['/missions/modifier/3', 'ROLE_GLA', 403], // Gla TEST has NOT created mission 3
            ['/missions/modifier/3', 'ROLE_VOLUNTEER', 403], // Volunteer TEST is NOT assigned to mission 3

            ['/missions/ajouter', 'ROLE_GLA', 200],
            ['/missions/ajouter', 'ROLE_VOLUNTEER', 403], // Volunteers can't create missions
        ];
    }


    /**
     * @group mission
     */
    public function testMissionsList()
    {
        $crawler = $this->clientA->request('GET', '/missions');

        // 4 missions in initial test db
        $this->assertSame(4, $crawler->filter('tbody > tr')->count());
    }

    /**
     * Access form to create a new mission from /missions
     *
     * @group mission
     */
    public function testFromListToNewMission()
    {
        $crawler = $this->clientA->request('GET', '/missions');

        $link = $crawler->selectLink('Créer une fiche mission')->link();

        $crawler = $this->clientA->click($link);

        $this->assertSame(1, $crawler->filter('h2:contains("Créer une fiche mission")')->count());
    }

    /**
     * @group mission
     */
    public function testNewMission()
    {
        $crawler = $this->clientA->request('GET', '/missions/ajouter');

        $form = $crawler->selectButton('Créer la fiche mission')->form();

        $form['mission[gla]']->select('3');
        $form['mission[address]']->select('8');    // 35-37 RUE JULES BRUNARD 69007 LYON 7
        $form['mission[description]']->setValue('Test description de nouvelle mission');

        $crawler = $this->clientA->submit($form);
        $crawler = $this->clientA->followRedirect();

        $currentDate = date('d/m/Y');

        // New mission id is 5 because of test database initialization
        $this->assertSame(1, $crawler->filter('h2:contains("Fiche mission GLA-Bénévole n°5")')->count());   
        $this->assertSame(1, $crawler->filter('#status:contains("Créée")')->count());
        $this->assertSame(1, $crawler->filter('#dateCreated:contains("'.$currentDate.'")')->count());
        $this->assertSame(1, $crawler->filter('#gla:contains("Gla TEST")')->count());
        $this->assertSame(1, $crawler->filter('#volunteer:contains("")')->count());
        $this->assertSame(1, $crawler->filter('#address:contains("35-37 RUE JULES BRUNARD 69007 LYON 7")')->count());
        $this->assertSame(1, $crawler->filter('#description:contains("Test description de nouvelle mission")')->count());
    }

    /**
     * Access view of a mission from /missions
     *
     * @group mission
     */
    public function testFromListToViewMission()
    {
        $crawler = $this->clientA->request('GET', '/missions');

        $link = $crawler->filter('#mission-4')->selectLink('Voir')->link();

        $crawler = $this->clientA->click($link);

        $this->assertSame(1, $crawler->filter('h2:contains("Fiche mission GLA-Bénévole n°4")')->count());
    }

    /**
     * Access edit of a mission from /missions
     *
     * @group mission
     */
    public function testFromListToEditMission()
    {
        $crawler = $this->clientA->request('GET', '/missions');

        $link = $crawler->filter('#mission-4')->selectLink('Modifier')->link();

        $crawler = $this->clientA->click($link);

        $this->assertSame(1, $crawler->filter('h2:contains("Modifier la fiche mission n°4")')->count());
    }

    // /**
    //  * @group mission
    //  */
    // public function testEditMission()
    // {
    //     $crawler = $this->clientA->request('GET', '/missions/modifier/4');

    //     $form = $crawler->selectButton('Valider')->form();

    //     $form['mission[volunteer]']->select('2');
    //     $form['mission[dateAssigned]']->setValue('2018-03-14');
    //     $form['mission[info]']->setValue('Test infos complémentaires modifiées');

    //     $crawler = $this->clientA->submit($form);
    //     $crawler = $this->clientA->followRedirect();

    //     $this->assertSame(1, $crawler->filter('h2:contains("Fiche mission GLA-Bénévole n°4")')->count());
    //     $this->assertSame(1, $crawler->filter('#status:contains("Prise en charge")')->count());
    //     $this->assertSame(1, $crawler->filter('#dateAssigned:contains("14/03/2018")')->count());
    //     $this->assertSame(1, $crawler->filter('#volunteer:contains("Volunteer TEST")')->count());
    //     $this->assertSame(1, $crawler->filter('#info:contains("Test infos complémentaires modifiées")')->count());
    // }

    /**
     * @group mission
     */
    public function testDeleteMission()
    {
        $crawler = $this->clientA->request('GET', '/missions/modifier/4');

        $link = $crawler->selectLink('Supprimer')->link();

        $crawler = $this->clientA->click($link);
        $crawler = $this->clientA->followRedirect();

        $this->assertSame(1, $crawler->filter('h2:contains("Liste des fiches mission")')->count());
        $this->assertSame(1, $crawler->filter('.alert-success:contains("La fiche mission a bien été supprimée")')->count());
        $this->assertSame(0, $crawler->filter('#mission-4')->count());
    }


    /**
     * @group mission
     */
    public function testReopenClosedMission()
    {
        $crawler = $this->clientA->request('GET', '/missions/voir/1');

        $link = $crawler->selectLink('Rouvrir')->link();

        $crawler = $this->clientA->click($link);
        $crawler = $this->clientA->followRedirect();

        $this->assertSame(1, $crawler->filter('h2:contains("Fiche mission GLA-Bénévole n°1")')->count());
        $this->assertSame(1, $crawler->filter('.alert-success:contains("La fiche mission a bien été ré-ouverte")')->count());
        $this->assertSame(1, $crawler->filter('#status:contains("Terminée")')->count());
    }

    /**
     * @group mission
     */
    public function testCloseFinishedMission()
    {
        $crawler = $this->clientA->request('GET', '/missions/voir/1');

        $link = $crawler->selectLink('Fermer')->link();

        $crawler = $this->clientA->click($link);
        $crawler = $this->clientA->followRedirect();

        $this->assertSame(1, $crawler->filter('h2:contains("Fiche mission GLA-Bénévole n°1")')->count());
        $this->assertSame(1, $crawler->filter('.alert-success:contains("La fiche mission a bien été fermée")')->count());
        $this->assertSame(1, $crawler->filter('#status:contains("Fermée")')->count());
    }
}
