<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryFormType;
use App\Repository\CategoryRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin')]
class CategoryController extends AbstractController
{
    #[Route('/ajouter-une-categorie', name: 'create_category', methods: ['GET', 'POST'])]
    public function createCategory(Request $request, CategoryRepository $repository, SluggerInterface $slugger): Response
    {
        $category = new Category();

        $form = $this->createForm(CategoryFormType::class, $category)
            ->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $category->setCreatedAt(new DateTime());
            $category->setUpdatedAt(new DateTime());

            # L'alias nous servira pour construire l'url d'un article
            $category->setAlias($slugger->slug($category->getName()));

            $repository->save($category, true);

            $this->addFlash('success', "La catégorie est ajoutée avec succès !");
            return $this->redirectToRoute('show_dashboard');
        }

        return $this->render('admin/category/create.html.twig', [
            'form' => $form->createView()
        ]);
    } // end create()

    #[Route('/modifier-une-categorie/{id}', name: 'update_category', methods: ['GET', 'POST'])]
    public function updateCategory(Category $category, Request $request, CategoryRepository $repository, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(CategoryFormType::class, $category, [
            'category' => $category
        ])
            ->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $category->setUpdatedAt(new DateTime());

            # L'alias nous servira pour construire l'url d'un article
            $category->setAlias($slugger->slug($category->getName()));

            $repository->save($category, true);

            $this->addFlash('success', "La catégorie est modifiée avec succès !");
            return $this->redirectToRoute('show_dashboard');
        }

        return $this->render('admin/category/create.html.twig', [
            'form' => $form->createView(),
            'category' => $category
        ]);
    }

    #[Route('/archiver-une-category/{id}', 'solf_delete_category', ['GET'])]
    public function softDeleteCategory(Category $category, CategoryRepository $repository): Response 
    {
        $category->setDeletedAt(new Datetime());
        $repository->save($category, true);
        $this->addFlass('success', "La catégorie". $category->getName() ." a bien été archivé.");
        return $this->redirectToRoute('show_dashboard');
    }

     public function hardDeleteCategory(Category $category, CategoryRepository $repository ): Response
   
     { 
        $repository->remove($category, true);

        $this->addFlash( 'success', "La categorie a bien été supprimé définitiment");
        return $this->redirectToRoute('show_dashbord');
     }
     
} // end class