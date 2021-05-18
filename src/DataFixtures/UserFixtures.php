<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $user = new User('javisr@javi.com', 'Salram');
        $user2 = new User('test@test.com', 'Test');

        $user->setRoles(['ROLE_ADMIN']);
        $user->setPassword($this->passwordEncoder->encodePassword(
            $user,
            'javi'
        ));
        $user2->setPassword($this->passwordEncoder->encodePassword(
            $user2,
            'test'
        ));

        $user->setIsVerified(true);


        $manager->persist($user);
        $manager->persist($user2);
        $manager->flush();
    }
}
