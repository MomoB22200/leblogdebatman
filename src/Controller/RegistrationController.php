<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegistrationController extends AbstractController
{
    #[Route('/creer-un-compte/', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        // si l'utilisateur est deja connecté, on le redirige de force sur la page d'accueil du site
        if ($this->getUser()) {
            return $this->redirectToRoute('main_home');
        }

        // création d'un nouvel objet utilisateur
        $user = new User();

        //création d'un nouveau formulaire de création de compte, "branché" sur £user (pour l'hydrater)
        $form = $this->createForm(RegistrationFormType::class, $user);

        //remplissage de formulaire avec les données POST (qui sont $request)
        $form->handleRequest($request);

        //si le formulaire a bien été envoyer et ne possède pas d'erreur
        if ($form->isSubmitted() && $form->isValid()) {

            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            //hydratation de la date d'inscription du nouvel utilisateur
            $user->setRegistrationDate( new \DateTime() );

            $entityManager->persist($user);
            $entityManager->flush();

            //message flash de success
            $this->addFlash('success', 'votre compte a bien été crée avec succès !');

            // Redirection de l'utilisateur vers la page de connexion
            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
