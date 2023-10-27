<?php
namespace App\Service;


use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailService
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendEmail($titre)
    {
        $email = (new Email())
            ->from('bacargoudiaby@gmail.com')
            ->to('team@devphantom.com')
            ->subject('Post Added!')
            ->html('<p>Le post avec le titre '.$titre.' a été bien ajouté !</p>');

        $this->mailer->send($email);
        return true;
    }
}