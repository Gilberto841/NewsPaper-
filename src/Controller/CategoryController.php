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
    } // end update()

    #[Route('/archiver-une-category/{id}', name: 'soft_delete_category', methods: ['GET'])]
    public function softDeleteCategory(Category $category, CategoryRepository $repository): Response
    {
        $category->setDeletedAt(new Datetime());

        $repository->save($category, true);

        $this->addFlash('success', "La catégorie ". $category->getName() ." a bien été archivé.");
        return $this->redirectToRoute('show_dashboard');
    } // end softDelete()

    #[Route('/restaurer-une-categorie/{id}', name: 'restore_category', methods: ['GET'])]
    public function restoreCategory(Category $category, CategoryRepository $repository): Response
    {
        $category->setDeletedAt(null);

        $repository->save($category, true);

        $this->addFlash('success', "La catégorie ". $category->getName() ." a bien été restauré.");
        return $this->redirectToRoute('show_dashboard');
    } // end restore()

    #[Route('/supprimer-une-categorie/{id}', name: 'hard_delete_category', methods: ['GET'])]
    public function hardDeleteCategory(Category $category, CategoryRepository $repository): Response
    {
        $repository->remove($category, true);

        $this->addFlash('success', "La catégorie a bien été supprimé définitivement.");
        return $this->redirectToRoute('show_dashboard');
    } // end hardDelete()

} // end class