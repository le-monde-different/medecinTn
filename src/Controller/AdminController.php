<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\UtilisateurType;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(UtilisateurRepository $userRepository): Response
    {
        // Ensure the user has ROLE_ADMIN
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $users = $userRepository->findAll();

        return $this->render('admin/index.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/admin/user/new', name: 'admin_user_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $user = new Utilisateur();
        $form = $this->createForm(UtilisateurType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin');
        }

        return $this->render('admin/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/user/{id}/edit', name: 'admin_user_edit')]
    public function edit(Request $request, Utilisateur $user, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $form = $this->createForm(UtilisateurType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_admin');
        }

        return $this->render('admin/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/user/{id}/delete', name: 'admin_user_delete', methods: ['POST'])]
    public function delete(Request $request, Utilisateur $user, EntityManagerInterface $entityManager): RedirectResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin');
    }
}
