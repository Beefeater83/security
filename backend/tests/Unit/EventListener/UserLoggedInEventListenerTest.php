<?php

declare(strict_types=1);

namespace App\Tests\Unit\EventListener;

use App\Entity\User;
use App\Event\UserLoggedInEvent;
use App\EventListener\UserLoggedInEventListener;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\MailerInterface;

class UserLoggedInEventListenerTest extends TestCase
{
    public function testEmailDispatching(): void
    {
        $user = $this->createMock(User::class);
        $user->method('getEmail')->willReturn('test@diakonov-it.com');
        $user->method('getName')->willReturn('Pitt');

        $event = new UserLoggedInEvent($user);

        $logger = $this->createMock(LoggerInterface::class);
        $mailer = $this->createMock(MailerInterface::class);

        $mailer
            ->expects($this->once())
            ->method('send');

        $listener = new UserLoggedInEventListener($mailer, $logger);

        $listener($event);
    }

    public function testErrorIsLoggedWhenMailSendingFails(): void
    {
        $user = $this->createMock(User::class);
        $user->method('getEmail')->willReturn('test@diakonov-it.com');
        $user->method('getName')->willReturn('Pitt');

        $event = new UserLoggedInEvent($user);

        $mailer = $this->createMock(MailerInterface::class);
        $mailer
            ->method('send')
            ->willThrowException(new \RuntimeException('SMTP error'));

        $logger = $this->createMock(LoggerInterface::class);
        $logger
            ->expects($this->once())
            ->method('error')
            ->with(
                'Mailing failed',
                ['reason' => 'SMTP error']
            );

        $listener = new UserLoggedInEventListener($mailer, $logger);

        $listener($event);
    }
}
