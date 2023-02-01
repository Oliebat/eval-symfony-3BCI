<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Cinema;
use Faker\Factory;

use function PHPSTORM_META\type;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i <= 10; $i++) {

            $faker = Factory::create('us_US');
            $cinema = new Cinema();
            //TITLE FILM
            $cinema->setNom($faker->words(3, true));
            $cinema->setSynopsis($faker->words(10, true));
            $cinema->setType($faker->randomElement(['film', 'série']));
            $cinema->setCreatedAt($faker->dateTimeBetween('-6 months'));
            $manager->persist($cinema);
          
        }
        $manager->flush();
     
    }
}
