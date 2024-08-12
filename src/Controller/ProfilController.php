<?php

namespace App\Controller;

use App\Entity\Profil; // Importer l'entité Profil
use App\Form\ProfilType; // Assurez-vous d'avoir un formulaire pour Profil
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Attribute\Route as AttributeRoute;

class ProfilController extends AbstractController
{
    #[Route('/profil/{id}', name: 'user_profile')]
    public function index(int $id, EntityManagerInterface $entityManager): Response
    {
        $profil = $entityManager->getRepository(Profil::class)->findOneBy(['utilisateur' => $id]);


        if (!$profil) {
            throw $this->createNotFoundException('Profil non trouvé');
        }

        $hasPhoto = $profil->getPhotoProfil() !== null;

        return $this->render('profil/index.html.twig', [
            'profil' => $profil,
            'hasPhoto' => $hasPhoto,
        ]);
    }

  

    #[Route('/profil/modifier/photo/{id}', name: 'profil_modifier_photo')]
    public function modifierPhoto(Request $request, int $id, EntityManagerInterface $entityManager): Response
    {
        $profil = $entityManager->getRepository(Profil::class)->find($id);

        if (!$profil) {
            throw $this->createNotFoundException('Profil non trouvé');
        }

        $form = $this->createForm(ProfilType::class, $profil);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('photoProfil')->getData();

            if ($file) {
                $newFilename = uniqid() . '.' . $file->guessExtension();
                try {
                    $file->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors du téléchargement de la photo.');
                    return $this->redirectToRoute('profil_modifier_photo', ['id' => $id]);
                }

                $profil->setPhotoProfil($newFilename);
            }

            $entityManager->flush();
            $this->addFlash('success', 'Photo de profil mise à jour avec succès.');

            return $this->redirectToRoute('user_profile', ['id' => $id]);
        }

        return $this->render('profil/modifierPhoto.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}