<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Product;
use App\Entity\Customer;
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $userPasswordHasher;
    
    public function __construct(UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->userPasswordHasher = $userPasswordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Création d'une dizaine de téléphones ayant pour titre
        for ($i = 11; $i < 16; $i++) {
            $telephone = new Product;
            $telephone->setName('Iphone ' . $i . 'Pro');
            $telephone->setBrand('Apple');
            $telephone->setScreenSize('6.06 pouces');
            $telephone->setRam('6 Go');
            $manager->persist($telephone);
        }

        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();


        // Création de quelques customer pour l'exemple
        $listCustomer = [];

        $customer1 = new Customer;
        $customer1->setName('orange');
        $manager->persist($customer1);
        $manager->flush();
        $listCustomer[] = $customer1;

        $customer2 = new Customer;
        $customer2->setName('free');
        $manager->persist($customer2);
        $manager->flush();
        $listCustomer[] = $customer2;

        // Création de quelques user pour l'exemple
        $utilisateur1 = new User;
        $utilisateur1->setName('xavier');
        $utilisateur1->setFirstName('charles');
        $utilisateur1->setWork('manager');
        $utilisateur1->setCustomer($listCustomer[0]);
        $utilisateur1->setEmail('xavier.charles@hotmail.fr');
        $utilisateur1->setPassword($this->userPasswordHasher->hashPassword($utilisateur1, "xavier"));
        $manager->persist($utilisateur1);
        $manager->flush();

        $utilisateur2 = new User;
        $utilisateur2->setName('lensherr');
        $utilisateur2->setFirstName('eric');
        $utilisateur2->setWork('manager');
        $utilisateur2->setCustomer($listCustomer[1]);
        $utilisateur2->setEmail('lensherr.eric@hotmail.fr');
        $utilisateur2->setPassword($this->userPasswordHasher->hashPassword($utilisateur2, "lensherr"));
        $manager->persist($utilisateur2);
        $manager->flush();

        $utilisateur3 = new User;
        $utilisateur3->setName('summers');
        $utilisateur3->setFirstName('scott');
        $utilisateur3->setWork('employé');
        $utilisateur3->setCustomer($listCustomer[0]);
        $utilisateur3->setEmail('summers.scott@hotmail.fr');
        $utilisateur3->setPassword($this->userPasswordHasher->hashPassword($utilisateur3, "summers"));
        $manager->persist($utilisateur3);
        $manager->flush();
    }
}
