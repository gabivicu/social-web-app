<?php

namespace App\DataFixtures;

use App\Entity\MicroPost;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $countries = ['Romania', 'France', 'Germany', 'Italy', 'Spain'];

        foreach ($countries as $country) {
            $microPost = new MicroPost();
            $microPost->setTitle($country);
            $microPost->setText("This is a micro post about $country");
            $microPost->setCreated(new DateTime());
            $manager->persist($microPost);
        }

        $manager->flush();
    }
}
