<?php

namespace App\DataFixtures;

use App\Entity\Admin;
use App\Entity\Brand;
use App\Entity\Category;
use App\Entity\Contact;
use App\Entity\Product;
use App\Entity\Slider;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        //Création de 10 Catégorie
        for ($i = 0; $i < 10; $i++){
            $category = new Category();
            $category->setName('Category ' . $i);
            $manager->persist($category);

        }

        //Création de 10 marques
        for ($i = 0; $i<10; $i++){
            $brand = new Brand();
            $brand->setName($faker->company);
            $manager->persist($brand);
        }

        //Création de 10 Cafés
        for ($i = 0; $i < 10; $i++){
            $product = new Product();
            $product->setName('Café '.$i);
            $product->setDescription($faker->text);
            $product->setPrice(mt_rand(10, 100));
            $product->setNote(mt_rand(0, 20));
            $product->setFamily('Famille '.$i);
            $product->setCountry($faker->country);
            $product->setBestSeller($faker->boolean);
            $product->setBrand(brand: $brand->getId(mt_rand(1,10)));
            $product->setCategory($category->getId(mt_rand(1,10)));
            $manager->persist($product);
        }


        for ($i = 0; $i < 10; $i++){
            $contact = new Contact();
            $contact->setFirstname($faker->firstName);
            $contact->setLastname($faker->lastName);
            $contact->setEmail($faker->email);
            $contact->setPhone($faker->phoneNumber);
            $contact->setMessage($faker->realText);
            $manager->persist($contact);
        }

        for ($i = 0; $i < 10; $i++){
            $slider = new Slider();
            $slider->setTitle($faker->title);
            $slider->setButtonLink($faker->url);
            $slider->setButtonText($faker->text(10));
            $slider->setContent($faker->realText);
            $manager->persist($slider);
        }

        for ($i = 0; $i < 10; $i++){
            $admin = new Admin();
            $admin->setEmail($faker->email);
            $admin->setPassword($faker->password);
            $admin->setRoles((array)'ROLE_USER');
            $manager->persist($admin);
        }

        $manager->flush();
    }
}
