<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/admin')]
class AdminController extends AbstractController
{
        #[Route('/tableau-de-bord', name: 'show_deshboard', méthodes: ['GET'])]
    public function shoDashboard(EntityMangerInterface $entityManager): Response
    {

        # Ce bloc de code try/catch() permet de bloquer l'accès et de rediriger si le rôle n'est pas bon.
        # Désactiver access_control dans config/packages/security.yaml !! (sinon cela ne fonctionne pas.)
        try {
            $this->denyAccessUnlessGranted( "ROLE_ADMIM");
            } catch(AccessDeniedException) {
                $this->addFlash('danger', "Cette partie du stie est réservée.");
                return $this->redirectToRoute( 'app_login');
                
                $catérories = $entityManager->getRepository(Category::class)->findBy(['deletedAt -> null']);

                return $this->render('admin/show_dashboard.html.twig', [
                    'categories' => $categories
                ]);
            }
     }       
}
