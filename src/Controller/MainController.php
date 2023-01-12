<?php

namespace App\Controller;

use App\Form\EditPhotoType;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{

    /**
     * Controleur de la page d'accueil
     */
    #[Route('/', name: 'main_home')]
    public function home(): Response
    {
        return $this->render('main/home.html.twig');
    }

    /**
     * controleur de la page de profil
     *
     * aces reservé au connecter (role_user)
     */
    #[Route('/mon-profil/', name: 'main_profil')]
    #[IsGranted('ROLE_USER')]
    public function profil(): Response
    {

        return $this->render('main/profil.html.twig');
    }

    /**
     * Controleur de la page page de modification de la photo de profil
     */
    #[Route('/changer-photo-de-profil/', name: 'main_edit_photo')]
    #[IsGranted('ROLE_USER')]
    public function editPhoto(Request $request, ManagerRegistry $doctrine): Response
    {
        $form = $this->createForm(EditPhotoType::class);

        $form->handleRequest($request);

        //si le formulaire a été envoyer et sans erreurs
        if($form->isSubmitted() && $form->isValid()){

            $photo = $form->get('photo')->getData();

            $newFileName = 'user' . $this->getUser()->getId() . '.' . $photo->guessExtension();

            //mise a jour du nom de la photo du compte connecté
            $this->getUser()->setPhoto($newFileName);
            $em = $doctrine->getManager();
            $em->flush();

            //sauvegarde physique de la photo coté serveur
            $photo->move(
                $this->getParameter('app.user.photo.directory'),
                $newFileName
            );

            //message de succès
            $this->addFlash('success', 'Photo de profil modifiée avec succès');

            //redirection vers la page de profil
            return $this->redirectToRoute('main_profil');
        }

        return $this->render('main/edit_photo.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}
