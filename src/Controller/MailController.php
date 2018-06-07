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
    private $senderAddress;
    private $senderName;

    public function __construct()
    {
        $this->mailer = new \SendGrid(getenv('SENDGRID_API_KEY'));
        $this->senderAddress = 'a.dahan@habitat-humanisme.org';
        $this->senderName = 'Alys';
    }
    
    public function send($subject, $to, $view)
    {
        $email = new \SendGrid\Mail\Mail();
        $email->setFrom($this->senderAddress, $this->senderName);
        $email->setSubject($subject);
        $email->addTo($to['email'], $to['name']);
        $email->addContent(
            "text/html", $view
        );

        try {
            $response = $this->mailer->send($email);
            print $response->statusCode() . "\n";
            print_r($response->headers());
            print $response->body() . "\n";
        } catch (\Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
    }
}