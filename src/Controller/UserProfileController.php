<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\UserProfileService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class UserProfileController extends AbstractController
{
    public function __construct(private UserProfileService $userProfileService)
    {
    }

    #[Route('/user/{id}', name: 'app_user_profile')]
    public function show(int $id): Response
    {
        $user = $this->userProfileService->getUserWithRelations($id);

        if (!$user) {
            throw $this->createNotFoundException('User not found.');
        }

        // Only load feed posts when viewing your own profile
        $feedPosts = [];
        if ($this->getUser() && $this->getUser()->getId() === $user->getId()) {
            $feedPosts = $this->userProfileService->getFeedPosts($user);
        }

        return $this->render('user_profile/show.html.twig', [
            'user' => $user,
            'profile' => $user->getUserProfile(),
            'posts' => $user->getMicroPosts(),
            'feedPosts' => $feedPosts,
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/user/{id}/follow', name: 'app_user_follow', methods: ['POST'])]
    public function follow(int $id): Response
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();
        $this->userProfileService->follow($currentUser, $id);

        return $this->redirectToRoute('app_user_profile', ['id' => $id]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/user/{id}/unfollow', name: 'app_user_unfollow', methods: ['POST'])]
    public function unfollow(int $id, Request $request): Response
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();
        $this->userProfileService->unfollow($currentUser, $id);

        // Redirect back to the page that initiated the unfollow (e.g. own profile following tab)
        $redirectTo = $request->request->get('redirect_to');
        if ($redirectTo) {
            return $this->redirect($redirectTo);
        }

        return $this->redirectToRoute('app_user_profile', ['id' => $id]);
    }
}
