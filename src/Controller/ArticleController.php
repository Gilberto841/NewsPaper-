<?php

namespace App\Controller;

use DateTime;
use App\Entity\Article;
use App\Form\ArticleFormType;
use App\Repository\ArticleRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ArticleController extends AbstractController
{
    #[Route('/ajouter-une-categorie', name: 'create_article', methods: ['GET', 'POST'])]
    public function createarticle(ArticleRepository $repository, Request $request, SluggerInterface $slugger): Response
    {
        $article = new Article();

        $form = $this->createForm(ArticleFormType::class, $article)
            ->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

        }

        return $this->render('admin/article/crate.html.twig', [
            'form' => $form ] );
            # L'alias nous servira pour construire l'url d'un article
            // $article->setAlias($slugger->slug($article->getName()));

            $repository->save($article, true);

            $this->addFlash('success', "La catégorie est ajoutée avec succès !");
            return $this->redirectToRoute('show_dashboard');
    

        return $this->render('admin/article/create.html.twig', [
            'form' => $form->createView()
        ]);
    }
    } // end create()   

