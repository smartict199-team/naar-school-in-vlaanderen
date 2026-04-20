<?php

declare(strict_types=1);

namespace App\Service\Mailer;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class UserMailer
{
    private MailerInterface $mailer;
    private string $senderEmail;
    private string $senderName;

    public function __construct(MailerInterface $mailer, string $senderEmail, string $senderName)
    {
        $this->mailer = $mailer;
        $this->senderEmail = $senderEmail;
        $this->senderName = $senderName;
    }

    public function sendPasswordSetupEmail(string $recipientEmail, string $ticketUrl): void
    {
        $email = (new TemplatedEmail())
            ->from(new Address($this->senderEmail, $this->senderName))
            ->to($recipientEmail)
            ->subject('Stel uw wachtwoord in voor Naar School in Vlaanderen')
            ->htmlTemplate('emails/password_setup.html.twig')
            ->context([
                'ticket_url' => $ticketUrl,
            ]);

        $this->mailer->send($email);
    }
}
