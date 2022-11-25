<?php

namespace App\DataFixtures;

use App\Entity\Program;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ProgramFixtures extends Fixture implements DependentFixtureInterface
{
    public const PROGRAM = [
        'Waling dead',
        'Vikings',
        'The boys',
        'The witcher',
        'Squid Game'
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::PROGRAM as $key => $programs) {
            $program = new Program();
            $program->setTitle($programs);
            $program->setSynopsis('Des zombies envahissent la terre');
            $program->setCategory($this->getReference('category_Action'));
            $manager->persist($program);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        // TODO: Implement getDependencies() method.
        return [
            CategoryFixtures::class,
        ];
    }
}
