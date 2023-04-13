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
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

#[Route('/admin')]
class ArticleController extends AbstractController
{
    // --------------------------------- CREATE-ARTICLE ---------------------------------
    #[Route('/ajouter-article', name: 'create_article', methods: ['GET', 'POST'])]
    public function createArticle(Request $request, ArticleRepository $repository, SluggerInterface $slugger): Response
    {
        $article = new Article();

        $form = $this->createForm(ArticleFormType::class, $article)
            ->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()) {

            $article->setCreatedAt(new DateTime());
            $article->setUpdatedAt(new DateTime());
            $article->setAlias($slugger->slug($article->getTitle()));

            # Set de la relation entre Article et User
            $article->setAuthor($this->getUser());

            /** @var UploadedFile $photo */ // - pour activer les actions get....
            $photo = $form->get('photo')->getData();

            if($photo) {
                $this->handleFile($article, $photo, $slugger);
            } // end if($photo)
            
            $repository->save($article, true);

            $this->addFlash('success', "L'article a été ajouté avec succès !");
            return $this->redirectToRoute('show_dashboard');
        } // end if($form)

        return $this->render('admin/article/create.html.twig', [
            'form' => $form->createView()
        ]);
    } //end createArticle()
    // ----------------------------------------------------------------------------------



    // --------------------------------- UPDATE-ARTICLE ---------------------------------
    #[Route('/modifier-article/{id}', name: 'update_article', methods: ['GET', 'POST'])]
    public function updateArticle(Article $article, Request $request, ArticleRepository $repository, SluggerInterface $slugger): Response
    {
        # Récupération de la photo non update
        $currentPhoto = $article->getPhoto();

        $form = $this->createForm(ArticleFormType::class, $article, [
            'photo' => $currentPhoto,
        ])->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $article->setUpdatedAt(new DateTime());
            $article->setAlias($slugger->slug($article->getTitle()));

            $photo = $form->get('photo')->getData();

            if($photo) {
                $this->handleFile($article, $photo, $slugger);
                unlink($this->getParameter('uploads_dir') . '/' . $currentPhoto);
                
            } else {
                # Si pas de nouvelle photo, alors on re-set la photo déjà dans la BDD
                $article->setPhoto($currentPhoto);
            } // end if($newPhoto)

            $repository->save($article, true);

            $this->addFlash('success',"La modification a bien été enregistrée.");
            return $this->redirectToRoute('show_dashboard');

        } // end if($form)

        return $this->render('admin/article/create.html.twig', [
            'form' => $form->createView(),
            'article' => $article
        ]);
    } // end updateArticle()
    // ----------------------------------------------------------------------------------



    // ------------------------------ SOFT-DELETE-ARTICLE -------------------------------
    #[Route('/archiver-un-article/{id}', name: 'soft_delete_article', methods: ['GET'])]
    public function softDeleteArticle(Article $article, ArticleRepository $repository): Response
    {
        $article->setDeletedAt(new DateTime());

        $repository->save($article, true);

        $this->addFlash('success', "L'article a bien été archivé !");
        return $this->redirectToRoute('show_dashboard');
    } // end softDeleteArticle()
    // ----------------------------------------------------------------------------------



    // -------------------------------- RESTORE-ARTICLE ---------------------------------
    #[Route('/restaurer-article/{id}', name: 'restore_article', methods: ['GET'])]
    public function restoreArticle(Article $article, ArticleRepository $repository): Response
    {
        $article->setDeletedAt(null);

        $repository->save($article, true);

        $this->addFlash('success', "L'article a bien été restauré !");
        return $this->redirectToRoute('show_dashboard');
    } // end restoreArticle()
    // ----------------------------------------------------------------------------------



    // ------------------------------ HARD-DELETE-ARTICLE -------------------------------
    #[Route('/supprimer-article/{id}', name: 'hard_delete_article', methods: ['GET'])]
    public function hardDeleteArticle(Article $article, ArticleRepository $repository): Response
    {
        $repository->remove($article, true);

        $this->addFlash('success', "L'article a bien été supprimé définitivement !");
        return $this->redirectToRoute('show_dashboard');
    } // end hardDeleteArticle()
    // ----------------------------------------------------------------------------------


    # -------------------------------------- PRIVATE FUNCTIONS ----------------------------------------------
private function handleFile(Article $article, UploadedFile $photo, SluggerInterface $slugger)
{
    # 1 - Déconstruire le nom du fichier
    # a : Variabiliser l'extension du fichier
    $extension = '.' . $photo->guessExtension() ;

    # 2 - Assainir le nom du fichier (càd, retirer les accents et les espaces blancs)
    $safeFilename = $slugger->slug(pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME));

    # 3 - Rendre le nom du fichier unique
    # a : Reconstruire le nom du fichier
    $newFilename = $safeFilename . '_' . uniqid("", true) . $extension;

    # 4 - Déplacer le fichier (upload dans notre application Symfony)
    # On utilise le try/catch lorsqu'une méthode lance (throw) une Exception (erreur)
    try {
        # On a défini un paramètre dans config/service.yaml qui est le chemin (absolu) du dossier 'uploads'
        # On récupère la valeur (le paramètre) avec getParameter() et le nom du param défini dans le fichier service.yaml.
        $photo->move($this->getParameter('uploads_dir'), $newFilename);
        # Si tout s'est bien passé (aucune Exception lancée) alors on doit set le nom de la photo en BDD
        $article->setPhoto($newFilename);
    }
    catch(FileException $exception) {
        $this->addFlash('warning', "Le fichier ne s'est pas importé correctement. Veuillez réessayer." . $exception->getMessage());
    } // end catch()
} // end handleFile()
# -------------------------------------- PRIVATE FUNCTIONS FIN -------------------------------------------



} // end of ArticleController{}