<?php

namespace App\MessageHandler;

use App\Entity\Notification;
use App\Message\SendNotification;
use App\Repository\NotificationRepository;
use App\Repository\UserRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class SendNotificationHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private NotificationRepository $notificationRepository,
    ) {
    }

    public function __invoke(SendNotification $message): void
    {
        $owner = $this->userRepository->find($message->getOwnerId());
        $actor = $this->userRepository->find($message->getActorId());

        if (!$owner || !$actor) {
            return;
        }

        $notification = new Notification();
        $notification->setOwner($owner);
        $notification->setActor($actor);
        $notification->setType($message->getType());

        $this->notificationRepository->save($notification);
    }
}
