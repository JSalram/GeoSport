<?php

namespace App\DataFixtures;

use App\Entity\Provincia;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProvinciaFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $provincias = [
            'A Coruña', 'Álava', 'Albacete', 'Alicante', 'Almería',
            'Asturias', 'Ávila', 'Badajoz', 'Baleares', 'Barcelona',
            'Burgos', 'Cáceres', 'Cádiz', 'Cantabria', 'Castellón',
            'Ciudad Real', 'Córdoba', 'Cuenca', 'Girona', 'Granada',
            'Guadalajara', 'Gipuzkoa', 'Huelva', 'Huesca', 'Jaén',
            'La Rioja', 'Las Palmas', 'León', 'Lérida', 'Lugo',
            'Madrid', 'Málaga', 'Murcia', 'Navarra', 'Ourense',
            'Palencia', 'Pontevedra', 'Salamanca', 'Segovia', 'Sevilla',
            'Soria', 'Tarragona', 'Santa Cruz de Tenerife', 'Teruel', 'Toledo',
            'Valencia', 'Valladolid', 'Vizcaya', 'Zamora', 'Zaragoza'
        ];

        foreach ($provincias as $p) {
            $provincia = new Provincia($p);
            $manager->persist($provincia);
        }

        $manager->flush();
    }
}
