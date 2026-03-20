<?php

namespace App\MessageHandler;

use App\Message\SendEmailVerification;
use App\Repository\UserRepository;
use App\Security\EmailVerifier;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Mime\Address;

#[AsMessageHandler]
class SendEmailVerificationHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private EmailVerifier $emailVerifier,
    ) {
    }

    public function __invoke(SendEmailVerification $message): void
    {
        $user = $this->userRepository->find($message->getUserId());

        if (!$user) {
            return;
        }

        $this->emailVerifier->sendEmailConfirmation(
            'app_verify_email',
            $user,
            (new TemplatedEmail())
                ->from(new Address('accounts@micropost.com', 'Micropost Symfony 6'))
                ->to((string) $user->getEmail())
                ->subject('Please Confirm your Email')
                ->htmlTemplate('registration/confirmation_email.html.twig')
        );
    }
}
