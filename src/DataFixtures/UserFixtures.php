<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;


class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setEmail('hagioswilson@gmail.com');
        $user->setName('Emmanuel Oteng Wilson');
        $user->setRoles(['ROLE_ADMIN']);
        $user->setPassword($this->passwordHasher->hashPassword($user, 'password'));
        $manager->persist($user);
        $manager->flush();

        $user1 = new User();
        $user1->setEmail('hagiose@yahoo.com');
        $user1->setName('Hagios Wilson');
        $user1->setRoles(['ROLE_MODERATOR']);
        $user1->setPassword($this->passwordHasher->hashPassword($user, 'password'));
        $manager->persist($user1);
        $manager->flush();
    }
}
