<?php

namespace App\DataFixtures;

use App\Entity\Deporte;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class DeporteFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $skate = new Deporte('skate');
        $surf = new Deporte('surf');

        $manager->persist($skate);
        $manager->persist($surf);
        $manager->flush();
    }
}
