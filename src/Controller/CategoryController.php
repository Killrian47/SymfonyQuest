<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\ProgramRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/category', name: 'category_')]
class CategoryController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(CategoryRepository $categoryRepository): Response
    {
        $category = $categoryRepository->findAll();

        return $this->render('category/index.html.twig', [
            'categories' => $category,
        ]);
    }

    #[Route('/{categoryName}', name: 'show')]
    public function show(string $categoryName, CategoryRepository $categoryRepository, ProgramRepository $programRepository): Response
    {
        $getCategory = $categoryRepository->findBy(['name' => $categoryName]);
        var_dump($getCategory);
        die();

        if (!$getCategory) {
            throw $this->createNotFoundException('Aucune catégorie nommée ' . $categoryName );
        }


        return $this->render('category/show.html.twig', [
            'programs' => $getCategory
        ]);
    }
}
