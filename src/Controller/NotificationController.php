<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\NotificationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
final class NotificationController extends AbstractController
{
    public function __construct(
        private NotificationRepository $notificationRepository,
    ) {
    }

    #[Route('/notifications', name: 'app_notifications')]
    public function index(): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $notifications = $this->notificationRepository->findByOwner($user);
        $this->notificationRepository->markAllSeen($user);

        return $this->render('notification/index.html.twig', [
            'notifications' => $notifications,
        ]);
    }
}
