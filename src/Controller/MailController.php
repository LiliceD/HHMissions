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
        $this->mailgun = new Mailgun('key-3efebd78f844e0a32233de9ef4aea0d1', new \Http\Adapter\Guzzle6\Client());
        $this->domain = 'alys.amoki.fr'; //getenv('MAILGUN_API_KEY')
    }
    
    public function send($subject, $to, $view)
    {
        $result = $this->mailgun->sendMessage("$this->domain", [
            'from'    => 'Alys <a.dahan@habitat-humanisme.org>',
            'to'      => '<lilice.dhn@gmail.com>',
            'subject' => $subject,
            'html'    => $view
        ]);
    }
}