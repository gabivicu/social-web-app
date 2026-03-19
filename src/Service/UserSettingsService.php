<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\UserProfile;
use App\Repository\UserProfileRepository;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class UserSettingsService
{
    public function __construct(
        private UserProfileRepository $userProfileRepository,
        private SluggerInterface $slugger,
        private string $avatarsDir,
    ) {
    }

    public function getOrCreateProfile(User $user): UserProfile
    {
        $profile = $user->getUserProfile();

        if (!$profile) {
            $profile = new UserProfile();
            $profile->setUser($user);
        }

        return $profile;
    }

    /**
     * Handles avatar upload: deletes old image, generates a unique filename, moves the file.
     *
     * @return bool true if the upload succeeded, false on failure
     */
    public function handleImageUpload(UserProfile $profile, UploadedFile $imageFile): bool
    {
        // Delete old image if exists
        $oldImage = $profile->getImage();
        if ($oldImage) {
            $oldPath = $this->avatarsDir . '/' . $oldImage;
            if (file_exists($oldPath)) {
                unlink($oldPath);
            }
        }

        // Generate unique filename
        $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

        try {
            $imageFile->move($this->avatarsDir, $newFilename);
            $profile->setImage($newFilename);

            return true;
        } catch (FileException $e) {
            return false;
        }
    }

    public function saveProfile(UserProfile $profile): void
    {
        $this->userProfileRepository->save($profile);
    }
}
