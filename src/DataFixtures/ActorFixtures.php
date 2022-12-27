<?php

namespace App\DataFixtures;


use App\Entity\Actor;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;


class ActorFixtures extends Fixture implements DependentFixtureInterface
{

    public function load(ObjectManager $manager): void
    {
        for ($i=1;$i<=10;$i++) {
            $actor= new Actor();
            $actor->setName('Actor_' . $i);
            for ($j = 1; $j<=3; $j++) {
                $actor->addProgram($this->getReference('program_Title_' . rand(1, 24)));
            }
            $manager->persist($actor);
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        // TODO: Implement getDependencies() method.
        return [
            ProgramFixtures::class,
        ];
    }
}
