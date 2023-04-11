<?php

namespace App\DataFixtures;

use App\Entity\Client;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;


class AppFixtures extends Fixture
{
    public function __construct(
        private PasswordHasherFactoryInterface $passwordHasherFactory,
    ) {
    }
    public function load(ObjectManager $manager): void
    {
        $client = new Client();
        $client->setEmail("clientea@gmail.com");
        $client->setRoles(['ROLE_USER']);
        $client->setPassword($this->passwordHasherFactory->getPasswordHasher(Client::class)->hash('1234'));
        $manager->persist($client);
        $client = new Client();
        $client->setEmail("clienteb@gmail.com");
        $client->setRoles(['ROLE_USER']);
        $client->setPassword($this->passwordHasherFactory->getPasswordHasher(Client::class)->hash('1234'));
        $manager->persist($client);
        $client = new Client();
        $client->setEmail("clientec@gmail.com");
        $client->setRoles(['ROLE_USER']);
        $client->setPassword($this->passwordHasherFactory->getPasswordHasher(Client::class)->hash('1234'));
        $manager->persist($client);

        $manager->flush();
    }
}
