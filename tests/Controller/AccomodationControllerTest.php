<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AccomodationControllerTest extends WebTestCase
{
    public static function setUpBeforeClass()
    {
        shell_exec('php bin/console doctrine:database:import habhum-tests-drop.sql');
        shell_exec('php bin/console doctrine:database:import habhum-tests-import.sql');
    }


    /**
     * @group url
     * 
     * @dataProvider accomodationUrls
     */
    public function testAccomodationUrls($url)
    {
        $client = static::createClient();

        $client->request('GET', $url);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
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
        $client = static::createClient();

        $crawler = $client->request('GET', '/logements');

        // 4 accomodations in initial test db
        $this->assertSame(4, $crawler->filter('tbody > tr')->count());
    }

    /**
     * Access form to create a new accomodation from /logements
     *
     * @group accomodation
     */
    public function testNewAccomodationFromList()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/logements');

        $link = $crawler->selectLink('Ajouter un logement')->link();
        
        $crawler = $client->click($link);

        $this->assertSame(1, $crawler->filter('h3:contains("Ajouter un logement")')->count());
    }

    /**
     * @group accomodation
     */
    public function testNewAccomodation()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/logements/ajouter');

        $form = $crawler->selectButton('Valider')->form();

        $form['accomodation[street]'] = '9 RUE MATHIEU VARILLE';
        $form['accomodation[postalCode]'] = '69007';
        $form['accomodation[city]'] = 'LYON 7';
        $form['accomodation[ownerType]']->select('FHH - Immeuble');

        $crawler = $client->submit($form);
        $crawler = $client->followRedirect();

        // New accomodation id is 12 because of test database initialization
        $this->assertSame(1, $crawler->filter('h3:contains("Liste des logements")')->count());
        $this->assertSame(1, $crawler->filter('.alert-success:contains("Le logement a bien été ajouté à la liste")')->count());     
        $this->assertSame(1, $crawler->filter('#accomodation-12:contains("9 RUE MATHIEU VARILLE")')->count());
        $this->assertSame(1, $crawler->filter('#accomodation-12:contains("69007")')->count());
        $this->assertSame(1, $crawler->filter('#accomodation-12:contains("LYON 7")')->count());
        $this->assertSame(1, $crawler->filter('#accomodation-12:contains("FHH - Immeuble")')->count());
    }

    /**
     * Access edit of the accomodation created in testNewAccomodation() from /logements
     *
     * @group accomodation
     */
    public function testEditAccomodationFromList()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/logements');

        $link = $crawler->filter('#accomodation-12')->selectLink('Modifier')->link();
        
        $crawler = $client->click($link);

        $form = $crawler->selectButton('Valider')->form();

        $this->assertSame(1, $crawler->filter('h3:contains("Modifier un logement")')->count());
        $this->assertEquals('9 RUE MATHIEU VARILLE', $crawler->filter('#accomodation_street')->attr('value'));
    }

    /**
     * Edit access of the accomodation created in testNewAccomodation()
     *
     * @group accomodation
     */
    public function testEditAccomodation()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/logements/modifier/12');

        $form = $crawler->selectButton('Valider')->form();

        $form['accomodation[access]'] = 'Digicode';

        $crawler = $client->submit($form);
        $crawler = $client->followRedirect();

        $this->assertSame(1, $crawler->filter('h3:contains("Liste des logements")')->count());
        $this->assertSame(1, $crawler->filter('.alert-success:contains("Les modifications ont bien été enregistrées")')->count());   
        $this->assertSame(1, $crawler->filter('#accomodation-12:contains("Digicode")')->count());
    }

    /**
     * Delete the accomodation created in testAddAccomodation()
     *
     * @group accomodation
     */
    public function testDeleteAccomodation()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/logements/modifier/12');

        $link = $crawler->selectLink('Supprimer')->link();
        
        $crawler = $client->click($link);
        $crawler = $client->followRedirect();

        $this->assertSame(1, $crawler->filter('h3:contains("Liste des logements")')->count());
        $this->assertSame(1, $crawler->filter('.alert-success:contains("Le logement a bien été supprimé")')->count());
        $this->assertSame(0, $crawler->filter('#accomodation-12')->count());
    }

}
