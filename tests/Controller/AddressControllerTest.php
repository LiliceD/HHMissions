<?php
//
//namespace App\Tests\Controller;
//
//use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
//
//class AddressControllerTest extends WebTestCase
//{
////    private $clientA; // client with ROLE_ADMIN
////    private $clientG; // client with ROLE_GLA
////    private $clientV; // client with ROLE_VOLUNTEER
////
////    public static function setUpBeforeClass()
////    {
////        shell_exec('php bin/console doctrine:database:import habhum-tests-drop.sql');
////        shell_exec('php bin/console doctrine:database:import habhum-tests-import.sql');
////    }
////
////    public function setUp()
////    {
////        // a.test is a dummy user with ROLE_ADMIN
////        $this->clientA = static::createClient(array(), array(
////            'PHP_AUTH_USER' => 'a.test',
////            'PHP_AUTH_PW'   => 'a.test',
////        ));
////
////        // g.test is a dummy user with ROLE_GLA
////        $this->clientG = static::createClient(array(), array(
////            'PHP_AUTH_USER' => 'g.test',
////            'PHP_AUTH_PW'   => 'g.test',
////        ));
////
////        // v.test is a dummy user with ROLE_VOLUNTEER
////        $this->clientV = static::createClient(array(), array(
////            'PHP_AUTH_USER' => 'v.test',
////            'PHP_AUTH_PW'   => 'v.test',
////        ));
////    }
////
////    /**
////     * @group url
////     *
////     * @dataProvider addressUrls
////     */
////    public function testAccomodationUrls($url, $role, $code)
////    {
////        if ($role === 'ROLE_ADMIN') {
////            $client = $this->clientA;
////        } else if ($role === 'ROLE_GLA') {
////            $client = $this->clientG;
////        } else if ($role === 'ROLE_VOLUNTEER') {
////            $client = $this->clientV;
////        }
////
////        $client->request('GET', $url);
////
////        $this->assertEquals($code, $client->getResponse()->getStatusCode());
////    }
////
////    public function addressUrls()
////    {
////        return [
////            ['/logements', 'ROLE_GLA', 200],
////            ['/logements', 'ROLE_VOLUNTEER', 200],
////            ['/logements/modifier/1', 'ROLE_GLA', 200],
////            ['/logements/modifier/1', 'ROLE_VOLUNTEER', 403], // Volunteers can't edit addresses
////            ['/logements/ajouter', 'ROLE_GLA', 200],
////            ['/logements/ajouter', 'ROLE_VOLUNTEER', 403] // Volunteers can't add addresses
////        ];
////    }
////
////
////    /**
////     * @group address
////     */
////    public function testAddressesList()
////    {
////        $crawler = $this->clientA->request('GET', '/logements');
////
////        // 4 addresses in initial test db
////        $this->assertSame(4, $crawler->filter('tbody > tr')->count());
////    }
////
////    /**
////     * Access form to create a new address from /logements
////     *
////     * @group address
////     */
////    public function testFromListToNewAccomodation()
////    {
////        $crawler = $this->clientA->request('GET', '/logements');
////
////        $link = $crawler->selectLink('Ajouter un logement')->link();
////
////        $crawler = $this->clientA->click($link);
////
////        $this->assertSame(1, $crawler->filter('h2:contains("Ajouter un logement")')->count());
////    }
////
////    /**
////     * @group address
////     */
////    public function testNewAccomodation()
////    {
////        $crawler = $this->clientA->request('GET', '/logements/ajouter');
////
////        $form = $crawler->selectButton('Valider')->form();
////
////        $form['address[street]'] = '9 RUE MATHIEU VARILLE';
////        $form['address[zipCode]'] = '69007';
////        $form['address[city]'] = 'LYON 7';
////        $form['address[ownerType]']->select('FHH - Immeuble');
////
////        $crawler = $this->clientA->submit($form);
////        $crawler = $this->clientA->followRedirect();
////
////        // New address id is 12 because of test database initialization
////        $this->assertSame(1, $crawler->filter('h2:contains("Liste des logements")')->count());
////        $this->assertSame(1, $crawler->filter('.alert-success:contains("Le logement a bien été ajouté")')->count());
////        $this->assertSame(1, $crawler->filter('#address-12:contains("9 RUE MATHIEU VARILLE")')->count());
////        $this->assertSame(1, $crawler->filter('#address-12:contains("69007")')->count());
////        $this->assertSame(1, $crawler->filter('#address-12:contains("LYON 7")')->count());
////        $this->assertSame(1, $crawler->filter('#address-12:contains("FHH - Immeuble")')->count());
////    }
////
////    /**
////     * Access edit of an address from /logements
////     *
////     * @group address
////     */
////    public function testFromListToEditAccomodation()
////    {
////        $crawler = $this->clientA->request('GET', '/logements');
////
////        $link = $crawler->filter('#address-5')->selectLink('Modifier')->link();
////
////        $crawler = $this->clientA->click($link);
////
////        $form = $crawler->selectButton('Valider')->form();
////
////        $this->assertSame(1, $crawler->filter('h2:contains("Modifier le logement")')->count());
////        $this->assertEquals('25 RUE DU PERRON', $crawler->filter('#address_street')->attr('value'));
////    }
////
////    /**
////     * @group address
////     */
////    public function testEditAccomodation()
////    {
////        $crawler = $this->clientA->request('GET', '/logements/modifier/5');
////
////        $form = $crawler->selectButton('Valider')->form();
////
////        $form['address[access]'] = 'Digicode';
////
////        $crawler = $this->clientA->submit($form);
////        $crawler = $this->clientA->followRedirect();
////
////        $this->assertSame(1, $crawler->filter('h2:contains("Liste des logements")')->count());
////        $this->assertSame(1, $crawler->filter('.alert-success:contains("Les modifications ont bien été enregistrées")')->count());
////        $this->assertSame(1, $crawler->filter('#address-5:contains("Digicode")')->count());
////    }
////
////    /**
////     * Delete the address created in testNewAccomodation()
////     *
////     * @group address
////     */
////    public function testDeleteAccomodation()
////    {
////        $crawler = $this->clientA->request('GET', '/logements/modifier/12');
////
////        $link = $crawler->selectLink('Supprimer')->link();
////
////        $crawler = $this->clientA->click($link);
////        $crawler = $this->clientA->followRedirect();
////
////        $this->assertSame(1, $crawler->filter('h2:contains("Liste des logements")')->count());
////        $this->assertSame(1, $crawler->filter('.alert-success:contains("Le logement a bien été supprimé")')->count());
////        $this->assertSame(0, $crawler->filter('#address-12')->count());
////    }
//
//}
