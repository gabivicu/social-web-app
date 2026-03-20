<?php

namespace App\Service;

use App\Entity\User;
use App\Message\SendEmailVerification;
use App\Repository\UserRepository;
use App\Security\EmailVerifier;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegistrationService
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
        private UserRepository $userRepository,
        private EmailVerifier $emailVerifier,
        private MessageBusInterface $messageBus,
    ) {
    }

    public function registerUser(User $user, string $plainPassword): void
    {
        $user->setPassword($this->passwordHasher->hashPassword($user, $plainPassword));

        $this->userRepository->save($user);
    }

    public function sendVerificationEmail(User $user): void
    {
        $this->messageBus->dispatch(new SendEmailVerification($user->getId()));
    }

    public function verifyUserEmail(Request $request, User $user): void
    {
        $this->emailVerifier->handleEmailConfirmation($request, $user);
        $user->setRoles(['ROLE_VERIFIED']);

        $this->userRepository->save($user);
    }
}
