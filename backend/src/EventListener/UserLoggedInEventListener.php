<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Event\UserLoggedInEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

#[AsEventListener]
class UserLoggedInEventListener
{
    public function __construct(
        private MailerInterface $mailer,
        #[Autowire(service: 'monolog.logger.security')]
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(UserLoggedInEvent $event): void
    {
        try {
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
        } catch (\Throwable $exception) {
            $this->logger->error('Mailing failed', [
                'reason' => $exception->getMessage(),
            ]);
        }
    }
}
