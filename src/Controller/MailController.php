<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class MailController
 *
 * @Route(service="app.mail_controller")
 *
 * @author Alice Dahan <lilice.dhn@gmail.com>
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
        $email->addTo($to);
        $email->addContent("text/html", $view);

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
