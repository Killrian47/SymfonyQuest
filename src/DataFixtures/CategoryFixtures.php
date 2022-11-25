<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends \Doctrine\Bundle\FixturesBundle\Fixture
{
    public const CATEGORY = [
        'Action',
        'Aventure',
        'Animation',
        'Fantastique',
        'Horreur'
    ];

    public function load(ObjectManager $manager)
    {
        foreach (self::CATEGORY as $key => $categoryName) {
            $category = new Category();
            $category->setName($categoryName);
            $manager->persist($category);
        }
        $manager->flush();
    }
}