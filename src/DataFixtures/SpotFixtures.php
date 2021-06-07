<?php

namespace App\DataFixtures;

use App\Entity\Spot;
use App\Entity\Valoracion;
use App\Repository\DeporteRepository;
use App\Repository\ProvinciaRepository;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class SpotFixtures extends Fixture implements DependentFixtureInterface
{
    private $userRepo;
    private $deporteRepo;
    private $provRepo;

    public function __construct(UserRepository $userRepo,
                                DeporteRepository $deporteRepo,
                                ProvinciaRepository $provRepo)
    {
        $this->userRepo = $userRepo;
        $this->deporteRepo = $deporteRepo;
        $this->provRepo = $provRepo;
    }

    public function load(ObjectManager $manager)
    {
        $skate = $this->deporteRepo->findOneBy(['nombre' => 'skate']);
        $surf = $this->deporteRepo->findOneBy(['nombre' => 'surf']);
        $provincia = $this->provRepo->findOneBy(['nombre' => 'Cádiz']);
        $user = $this->userRepo->findOneBy(['username' => 'Salram']);

        for ($i = 0; $i < 10; $i++) {
            $spot = new Spot(
                'Spot de prueba ' . ($i + 1),
                $i % 2 == 0 ? $skate : $surf,
                $provincia,
                null,
                $user
            );
            $spot->setAprobado(true);
            $spot->setCoord($provincia->getCoord());

            $valoracion = new Valoracion($i % 10, "Comentario de prueba", $user, $spot);
            $spot->addValoracion($valoracion);

            $manager->persist($valoracion);
            $manager->persist($spot);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            DeporteFixtures::class,
            ProvinciaFixtures::class,
        ];
    }
}
