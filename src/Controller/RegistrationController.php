<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Entity\Profil;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new Utilisateur();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {  
            // Encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()  
                )
               
            );
         
            $user->setDateInscription(new \DateTime());

            // Créer un profil pour l'utilisateur
            $profil = new Profil();
            $profil->setUtilisateur($user); // Associe l'utilisateur au profil

            // Persist both user and profile
            $entityManager->persist($user);
            $entityManager->persist($profil);
            $entityManager->flush();

         

            // Redirect to the user profile page
            return $this->redirectToRoute('user_profile', ['id' => $user->getId()]);
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }
}
