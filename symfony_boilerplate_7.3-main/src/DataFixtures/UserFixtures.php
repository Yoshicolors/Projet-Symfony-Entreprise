<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $admin = new User();
        $admin->setEmail('admin@backoffice.com');
        $admin->setFirstname('Jean');
        $admin->setLastname('Dupont');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'admin123'));
        $manager->persist($admin);

        $managerUser = new User();
        $managerUser->setEmail('manager@backoffice.com');
        $managerUser->setFirstname('Marie');
        $managerUser->setLastname('Martin');
        $managerUser->setRoles(['ROLE_MANAGER']);
        $managerUser->setPassword($this->passwordHasher->hashPassword($managerUser, 'manager123'));
        $manager->persist($managerUser);

        $user = new User();
        $user->setEmail('user@backoffice.com');
        $user->setFirstname('Pierre');
        $user->setLastname('Bernard');
        $user->setRoles(['ROLE_USER']);
        $user->setPassword($this->passwordHasher->hashPassword($user, 'user123'));
        $manager->persist($user);

        $manager->flush();
    }
}
