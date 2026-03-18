<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\UserProfile;
use App\Form\UserProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
final class UserSettingsController extends AbstractController
{
    #[Route('/settings', name: 'app_settings')]
    public function settings(Request $request, EntityManagerInterface $em): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $profile = $user->getUserProfile();

        if (!$profile) {
            $profile = new UserProfile();
            $profile->setUser($user);
        }

        $form = $this->createForm(UserProfileType::class, $profile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($profile);
            $em->flush();

            $this->addFlash('success', 'Settings saved successfully.');

            return $this->redirectToRoute('app_settings');
        }

        return $this->render('user_settings/index.html.twig', [
            'form' => $form,
        ]);
    }
}