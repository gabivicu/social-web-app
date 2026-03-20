<?php

namespace App\Service;

use App\Entity\Notification;
use App\Entity\User;
use App\Message\SendNotification;
use App\Repository\MicroPostRepository;
use App\Repository\UserRepository;
use Symfony\Component\Messenger\MessageBusInterface;

class UserProfileService
{
    public function __construct(
        private UserRepository $userRepository,
        private MicroPostRepository $microPostRepository,
        private MessageBusInterface $messageBus,
    ) {
    }

    public function getUserWithRelations(int $id): ?User
    {
        return $this->userRepository->findWithRelations($id);
    }

    public function getFeedPosts(User $user): array
    {
        return $this->microPostRepository->findByFollowedUsers($user->getId());
    }

    public function follow(User $currentUser, int $targetUserId): void
    {
        $userToFollow = $this->userRepository->find($targetUserId);

        if (!$userToFollow) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('User not found.');
        }

        $currentUser->follow($userToFollow);
        $this->userRepository->save($currentUser);

        $this->messageBus->dispatch(new SendNotification(
            $userToFollow->getId(),
            $currentUser->getId(),
            Notification::TYPE_FOLLOW,
        ));
    }

    public function unfollow(User $currentUser, int $targetUserId): void
    {
        $userToUnfollow = $this->userRepository->find($targetUserId);

        if (!$userToUnfollow) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('User not found.');
        }

        $currentUser->unfollow($userToUnfollow);
        $this->userRepository->save($currentUser);
    }
}
