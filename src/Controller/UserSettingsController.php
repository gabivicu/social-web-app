<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\UserProfile;
use App\Form\UserProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

#[IsGranted('ROLE_USER')]
final class UserSettingsController extends AbstractController
{
    #[Route('/settings', name: 'app_settings')]
    public function settings(Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
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
            $imageFile = $form->get('imageFile')->getData();

            if ($imageFile) {
                $uploadsDir = $this->getParameter('kernel.project_dir') . '/public/uploads/avatars';

                // Delete old image if exists
                $oldImage = $profile->getImage();
                if ($oldImage) {
                    $oldPath = $uploadsDir . '/' . $oldImage;
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }

                // Generate unique filename
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move($uploadsDir, $newFilename);
                    $profile->setImage($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Failed to upload image.');
                }
            }

            $em->persist($profile);
            $em->flush();

            $this->addFlash('success', 'Settings saved successfully.');

            return $this->redirectToRoute('app_settings');
        }

        return $this->render('user_settings/index.html.twig', [
            'form' => $form,
            'profile' => $profile,
        ]);
    }
}
