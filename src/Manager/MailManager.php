<?php

namespace App\Manager;

use SendGrid\Mail\TypeException;

/**
 * Class MailManager
 *
 * @author Alice Dahan <lilice.dhn@gmail.com>
 */
class MailManager
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

    /**
     * @param string $subject
     * @param string $to
     * @param string $view
     *
     * @throws TypeException
     */
    public function send(string $subject, string $to, string $view)
    {
        $email = new \SendGrid\Mail\Mail();
        $email->setFrom($this->senderAddress, $this->senderName);
        $email->setSubject($subject);
        $email->addTo($to);
        $email->addContent("text/html", $view);

        try {
            $response = $this->mailer->send($email);
            print $response->statusCode() . PHP_EOL;
            print_r($response->headers());
            print $response->body() . PHP_EOL;
        } catch (\Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), PHP_EOL;
        }
    }
}
