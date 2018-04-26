<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @Route(service="app.mail_controller")
 */
class MailController extends Controller
{
    private $mailer;

    public function __construct(\Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
    }
    
    public function send($subject, $to, $view)
    {
        $message = (new \Swift_Message($subject))
            ->setFrom('a.dahan@habitat-humanisme.org', 'Alys')
            ->setTo($to)
            ->setBody(
                $view,
                'text/html'
            )
        ;

        $this->mailer->send($message);
    }
}