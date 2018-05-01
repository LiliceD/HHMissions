<?php

namespace App\Controller;

use Mailgun\Mailgun;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @Route(service="app.mail_controller")
 */
class MailController extends Controller
{
    private $mailgun;
    private $domain;

    public function __construct()
    {
        $this->mailgun = new Mailgun(getenv('MAILGUN_API_KEY'), new \Http\Adapter\Guzzle6\Client());
        $this->domain = getenv('MAILGUN_DOMAIN');
    }
    
    public function send($subject, $to, $view)
    {
        $result = $this->mailgun->sendMessage("$this->domain", [
            'from'    => 'Alys <a.dahan@habitat-humanisme.org>',
            'to'      => $to,
            'subject' => $subject,
            'html'    => $view
        ]);
    }
}