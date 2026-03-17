<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Entity\MicroPost;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $countries = [
            'Romania' => 5,
            'France' => 0,
            'Germany' => 4,
            'Italy' => 0,
            'Spain' => 0,
        ];

        $comments = [
            'Great post, thanks for sharing!',
            'I totally agree with this.',
            'Very interesting perspective.',
            'I have been there, it was amazing!',
            'Can you tell us more about this?',
            'This is so cool!',
            'Love this content.',
            'Well written, keep it up!',
        ];

        foreach ($countries as $country => $likes) {
            $microPost = new MicroPost();
            $microPost->setTitle($country);
            $microPost->setText("This is a micro post about $country");
            $microPost->setCreated(new DateTime());
            $microPost->setLikes($likes);
            $manager->persist($microPost);

            $numComments = rand(2, 4);
            $shuffled = $comments;
            shuffle($shuffled);

            for ($i = 0; $i < $numComments; $i++) {
                $comment = new Comment();
                $comment->setText($shuffled[$i]);
                $comment->setPost($microPost);
                $manager->persist($comment);
            }
        }

        $manager->flush();
    }
}