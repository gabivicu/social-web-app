<?php

namespace App\Twig;

use App\Repository\NotificationRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class NotificationExtension extends AbstractExtension
{
    public function __construct(
        private NotificationRepository $notificationRepository,
        private Security $security,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('unseen_notification_count', [$this, 'getUnseenCount']),
        ];
    }

    public function getUnseenCount(): int
    {
        $user = $this->security->getUser();

        if (!$user) {
            return 0;
        }

        return $this->notificationRepository->countUnseen($user);
    }
}
