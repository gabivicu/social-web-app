<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserProfileType;
use App\Service\UserSettingsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
final class UserSettingsController extends AbstractController
{
    public function __construct(private UserSettingsService $userSettingsService)
    {
    }

    #[Route('/settings', name: 'app_settings')]
    public function settings(Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $profile = $this->userSettingsService->getOrCreateProfile($user);

        $form = $this->createForm(UserProfileType::class, $profile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageFile')->getData();

            if ($imageFile) {
                if (!$this->userSettingsService->handleImageUpload($profile, $imageFile)) {
                    $this->addFlash('error', 'Failed to upload image.');
                }
            }

            $this->userSettingsService->saveProfile($profile);

            $this->addFlash('success', 'Settings saved successfully.');

            return $this->redirectToRoute('app_settings');
        }

        return $this->render('user_settings/index.html.twig', [
            'form' => $form,
            'profile' => $profile,
        ]);
    }
}
