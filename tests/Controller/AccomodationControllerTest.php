<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AccomodationControllerTest extends WebTestCase
{
    private $clientA; // client with ROLE_ADMIN
    private $clientG; // client with ROLE_GLA
    private $clientV; // client with ROLE_VOLUNTEER

    public static function setUpBeforeClass()
    {
        shell_exec('php bin/console doctrine:database:import habhum-tests-drop.sql');
        shell_exec('php bin/console doctrine:database:import habhum-tests-import.sql');
    }

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
     * @dataProvider accomodationUrls
     */
    public function testAccomodationUrls($url)
    {
        $this->clientA->request('GET', $url);

        $this->assertEquals(200, $this->clientA->getResponse()->getStatusCode());
    }

    public function accomodationUrls()
    {
        return [
            ['/logements'],
            ['/logements/ajouter'],
            ['/logements/modifier/1']
        ];
    }


    /**
     * @group accomodation
     */
    public function testAccomodationsList()
    {
        $crawler = $this->clientA->request('GET', '/logements');

        // 4 accomodations in initial test db
        $this->assertSame(4, $crawler->filter('tbody > tr')->count());
    }

    /**
     * Access form to create a new accomodation from /logements
     *
     * @group accomodation
     */
    public function testFromListToNewAccomodation()
    {
        $crawler = $this->clientA->request('GET', '/logements');

        $link = $crawler->selectLink('Ajouter un logement')->link();
        
        $crawler = $this->clientA->click($link);

        $this->assertSame(1, $crawler->filter('h2:contains("Ajouter un logement")')->count());
    }

    /**
     * @group accomodation
     */
    public function testNewAccomodation()
    {
        $crawler = $this->clientA->request('GET', '/logements/ajouter');

        $form = $crawler->selectButton('Valider')->form();

        $form['accomodation[street]'] = '9 RUE MATHIEU VARILLE';
        $form['accomodation[postalCode]'] = '69007';
        $form['accomodation[city]'] = 'LYON 7';
        $form['accomodation[ownerType]']->select('FHH - Immeuble');

        $crawler = $this->clientA->submit($form);
        $crawler = $this->clientA->followRedirect();

        // New accomodation id is 12 because of test database initialization
        $this->assertSame(1, $crawler->filter('h2:contains("Liste des logements")')->count());
        $this->assertSame(1, $crawler->filter('.alert-success:contains("Le logement a bien été ajouté à la liste")')->count());     
        $this->assertSame(1, $crawler->filter('#accomodation-12:contains("9 RUE MATHIEU VARILLE")')->count());
        $this->assertSame(1, $crawler->filter('#accomodation-12:contains("69007")')->count());
        $this->assertSame(1, $crawler->filter('#accomodation-12:contains("LYON 7")')->count());
        $this->assertSame(1, $crawler->filter('#accomodation-12:contains("FHH - Immeuble")')->count());
    }

    /**
     * Access edit of an accomodation from /logements
     *
     * @group accomodation
     */
    public function testFromListToEditAccomodation()
    {
        $crawler = $this->clientA->request('GET', '/logements');

        $link = $crawler->filter('#accomodation-5')->selectLink('Modifier')->link();
        
        $crawler = $this->clientA->click($link);

        $form = $crawler->selectButton('Valider')->form();

        $this->assertSame(1, $crawler->filter('h2:contains("Modifier le logement")')->count());
        $this->assertEquals('25 RUE DU PERRON', $crawler->filter('#accomodation_street')->attr('value'));
    }

    /**
     * @group accomodation
     */
    public function testEditAccomodation()
    {
        $crawler = $this->clientA->request('GET', '/logements/modifier/5');

        $form = $crawler->selectButton('Valider')->form();

        $form['accomodation[access]'] = 'Digicode';

        $crawler = $this->clientA->submit($form);
        $crawler = $this->clientA->followRedirect();

        $this->assertSame(1, $crawler->filter('h2:contains("Liste des logements")')->count());
        $this->assertSame(1, $crawler->filter('.alert-success:contains("Les modifications ont bien été enregistrées")')->count());   
        $this->assertSame(1, $crawler->filter('#accomodation-5:contains("Digicode")')->count());
    }

    /**
     * Delete the accomodation created in testNewAccomodation()
     *
     * @group accomodation
     */
    public function testDeleteAccomodation()
    {
        $crawler = $this->clientA->request('GET', '/logements/modifier/12');

        $link = $crawler->selectLink('Supprimer')->link();
        
        $crawler = $this->clientA->click($link);
        $crawler = $this->clientA->followRedirect();

        $this->assertSame(1, $crawler->filter('h2:contains("Liste des logements")')->count());
        $this->assertSame(1, $crawler->filter('.alert-success:contains("Le logement a bien été supprimé")')->count());
        $this->assertSame(0, $crawler->filter('#accomodation-12')->count());
    }

}
