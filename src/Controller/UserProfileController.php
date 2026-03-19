<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\MicroPostRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class UserProfileController extends AbstractController
{
    #[Route('/user/{id}', name: 'app_user_profile')]
    public function show(int $id, UserRepository $userRepository, MicroPostRepository $microPostRepository): Response
    {
        $user = $userRepository->findWithRelations($id);

        if (!$user) {
            throw $this->createNotFoundException('User not found.');
        }

        // Only load feed posts when viewing your own profile
        $feedPosts = [];
        if ($this->getUser() && $this->getUser()->getId() === $user->getId()) {
            $feedPosts = $microPostRepository->findByFollowedUsers($user->getId());
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
    public function follow(int $id, UserRepository $userRepository, EntityManagerInterface $em): Response
    {
        $userToFollow = $userRepository->find($id);

        if (!$userToFollow) {
            throw $this->createNotFoundException('User not found.');
        }

        /** @var User $currentUser */
        $currentUser = $this->getUser();
        $currentUser->follow($userToFollow);
        $em->flush();

        return $this->redirectToRoute('app_user_profile', ['id' => $id]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/user/{id}/unfollow', name: 'app_user_unfollow', methods: ['POST'])]
    public function unfollow(int $id, UserRepository $userRepository, EntityManagerInterface $em, Request $request): Response
    {
        $userToUnfollow = $userRepository->find($id);

        if (!$userToUnfollow) {
            throw $this->createNotFoundException('User not found.');
        }

        /** @var User $currentUser */
        $currentUser = $this->getUser();
        $currentUser->unfollow($userToUnfollow);
        $em->flush();

        // Redirect back to the page that initiated the unfollow (e.g. own profile following tab)
        $redirectTo = $request->request->get('redirect_to');
        if ($redirectTo) {
            return $this->redirect($redirectTo);
        }

        return $this->redirectToRoute('app_user_profile', ['id' => $id]);
    }
}