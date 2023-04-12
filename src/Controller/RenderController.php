<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RenderController extends AbstractController

{
    #[Route('/catégories', 'render_categories_in_nav ')]
    public function renderCategoriesInNav(CategoryRepository $repository): Resoibse
    {
        $categories = $repository->findBy(['deletedAt' => null], ['name' => 'ASC']);
        
        return$this->render( 'render/categories_in_nav.html.twig' [
            'categories' => $categories
        ]);
    }
}
