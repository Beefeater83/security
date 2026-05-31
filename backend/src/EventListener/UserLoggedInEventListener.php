<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Event\UserLoggedInEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

#[AsEventListener]
class UserLoggedInEventListener
{
    public function __construct(private MailerInterface $mailer)
    {
    }

    public function __invoke(UserLoggedInEvent $event): void
    {
        $user = $event->user;
        $email = (new Email())
            ->from('no-reply@diakonov-it.com.ua')
            ->to($user->getEmail())
            ->subject('Login notification')
            ->html("
                <p style='font-size:14px; color:#333;'>
                Hello <strong>{$user->getName()}</strong>,
                you have successfully logged-in on security.diakonov-it.com.ua</p>
        ");
        $this->mailer->send($email);
    }
}
