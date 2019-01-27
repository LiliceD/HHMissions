<?php
//
//namespace App\Tests\Controller;
//
//use App\Controller\MissionController;
//use App\Form\MissionSearchType;
//use App\Form\MissionType;
//use PHPUnit\Framework\MockObject\MockObject;
//use Symfony\Bundle\FrameworkBundle\Client;
//use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
//use Symfony\Component\HttpFoundation\RedirectResponse;
//use Symfony\Component\HttpFoundation\Request;
//use Symfony\Component\HttpFoundation\Response;
//
///**
// * Class MissionControllerTest
// *
// * @coversDefaultClass \App\Controller\MissionController
// */
//class MissionControllerTest extends WebTestCase
//{
//    private $clientO; // new client
//    private $clientA; // client with ROLE_ADMIN
//    private $clientG; // client with ROLE_GLA
//    private $clientV; // client with ROLE_VOLUNTEER
//
//    public function setUp()
//    {
//        $this->clientO = static::createClient();
//
//        // a.test is a dummy user with ROLE_ADMIN
//        $this->clientA = static::createClient(array(), array(
//            'PHP_AUTH_USER' => 'a.test',
//            'PHP_AUTH_PW'   => 'a.test',
//        ));
//
//        // g.test is a dummy user with ROLE_GLA
//        $this->clientG = static::createClient(array(), array(
//            'PHP_AUTH_USER' => 'g.test',
//            'PHP_AUTH_PW'   => 'g.test',
//        ));
//
//        // v.test is a dummy user with ROLE_VOLUNTEER
//        $this->clientV = static::createClient(array(), array(
//            'PHP_AUTH_USER' => 'v.test',
//            'PHP_AUTH_PW'   => 'v.test',
//        ));
//    }
//
//    /**
//     * @dataProvider urlProvider
//     * @param int $responseCode
//     * @param bool $redirect
//     */
//    public function testAll(int $responseCode, bool $redirect = false)
//    {
//        /** @var Client $client */
//        $client = $this->clientA;
//        $client->request('GET', '/missions/1/voir');
//
//        $this->assertEquals($responseCode, $client->getResponse()->getStatusCode());
//    }
//
//    public function urlProvider()
//    {
//        return [
//            [Response::HTTP_OK],
//            [Response::HTTP_FOUND],
//            [Response::HTTP_FOUND],
//            [Response::HTTP_OK],
//            ['/missions/ajouter', 'ROLE_GLA', 200],
//            ['/missions/modifier/2', 'ROLE_GLA', 200], // Gla TEST has created mission 2
//            ['/missions/voir/1', 'ROLE_GLA', 200],
//            ['/missions/recap', 'ROLE_GLA', 200],
//        ];
//    }

//    public static function setUpBeforeClass()
//    {
//        shell_exec('php bin/console doctrine:database:import habhum-tests-drop.sql');
//        shell_exec('php bin/console doctrine:database:import habhum-tests-import.sql');
//    }
//

//
//

//    public function testMissionUrls($url, $role, $code)
//    {
//        if ($role === 'ROLE_ADMIN') {
//            $client = $this->clientA;
//        } else if ($role === 'ROLE_GLA') {
//            $client = $this->clientG;
//        } else if ($role === 'ROLE_VOLUNTEER') {
//            $client = $this->clientV;
//        }
//
//        $client->request('GET', $url);
//
//        $this->assertEquals($code, $client->getResponse()->getStatusCode());
//    }
//

//}
