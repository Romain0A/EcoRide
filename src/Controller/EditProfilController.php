<?php

namespace App\Controller;

use App\Form\UtilisateurType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class EditProfilController extends AbstractController
{
    #[Route('/edit_profil', name: 'app_edit_profil')]
    public function index(Request $request,EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        $form = $this->createForm(UtilisateurType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

        $photo = $form->get('photo')->getData();
        if ($photo) {

            if ($user->getPhoto() && $user->getPhoto() !== 'img/icones/material-symbols--account-box.png') {
                $oldPath = $this->getParameter('kernel.project_dir').'/public/'.$user->getPhoto();
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }

            $photoNom = uniqid().'.'.$photo->guessExtension();

            $photo->move(
                $this->getParameter('kernel.project_dir').'/public/img/pfp', $photoNom
            );
            $user->setPhoto('img/pfp/'.$photoNom);
        }

        $em->flush();
        return $this->redirectToRoute('app_espace_utilisateur');
        }

        return $this->render('editProfil.html.twig', ['form' => $form->createView(),]);
    }
}
