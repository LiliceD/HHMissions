<?php

namespace App\Controller;

use Mailjet\Resources;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @Route(service="app.mail_controller")
 */
class MailController extends Controller
{
    private $apikey;
    private $apisecret;
    private $mj;

    public function __construct()
    {
        $this->apikey = getenv('MAILJET_APIKEY_PUBLIC');
        $this->apikey = getenv('MAILJET_APIKEY_PRIVATE');
        $this->mj = new \Mailjet\Client($this->apikey, $this->apisecret, true, ['version' => 'v3.1']);
    }
    
    public function send($subject, $to, $view)
    {
        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => 'contact@alys.amoki.fr',
                        'Name' => 'Alys'
                    ],
                    'To' => [
                        [
                            'Email' => $to['email'],
                            'Name' => $to['name']
                        ]
                    ],
                    'Subject' => $subject,
                    'HTMLPart' => $view
                ]
            ]
        ];

        $this->mj->post(Resources::$Email, ['body' => $body]);
    }
}