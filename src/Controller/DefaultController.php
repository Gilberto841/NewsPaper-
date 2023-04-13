<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    #[Route('/', name: 'show_home', methods: ['GET'])]
    public function showHome(): Response
    {
        return $this->render('default/show_home.html.twig', [
            'article' => $articles
        ]);
    } // end function showHome()

    #[Route('/voir-articles/{alias', 'show_articles_from_cat' ['GET'])]
    public function showArticlesFromCatigory(): Response 
    {
        $articles = $articleRepository->finBy([
            'deletedAt' => null,
            'category' => $category->getId()

        ]);
        return $this->render( 'defaul')

    }

} // end class