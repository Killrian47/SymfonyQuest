<?php

namespace App\DataFixtures;

use App\Entity\Program;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\SluggerInterface;


class ProgramFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public function getDependencies()
    {
        return [
            CategoryFixtures::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {

        $numberOfCategories = count(CategoryFixtures::CATEGORIES);
        for ($i = 1; $i <= 25; $i++) {
            $program = new Program();
            $program->setTitle('Title_' . $i);
            $program->setSynopsis('Synopsis ' . $i);
            $program->setPoster('Link to poster ' . $i);
            $program->setCountry('USA');
            $program->setYear(2000);
            $program->setCategory($this->getReference('category_' . CategoryFixtures::CATEGORIES[rand(0, $numberOfCategories - 1)]));
            $slug = $this->slugger->slug($program->getTitle());
            $program->setSlug($slug);
            $manager->persist($program);
            $this->addReference('program_' . $program->getTitle(), $program);
        }
        $manager->flush();
    }
}
