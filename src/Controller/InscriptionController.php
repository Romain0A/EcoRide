<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\InscriptionType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class InscriptionController extends AbstractController
{
    #[Route('/inscription', name: 'app_inscription')]
    public function index(EntityManagerInterface $emi,UserPasswordHasherInterface $passwordHasher,Request $request): Response
    {

        if ($this->getUser()) {
            return $this->redirectToRoute('app_accueil');
        }

        $utilisateur = new Utilisateur();
        $form = $this->createForm(InscriptionType::class, $utilisateur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            $plainPassword = $form->get('plainPassword')->getData();
            $motDePasse = $passwordHasher->hashPassword(
                $utilisateur,
                $plainPassword
            );
            $utilisateur->setPassword($motDePasse);

            $photo = $form->get('photo')->getData();
            if ($photo) {
                $photoNom = uniqid().'.'.$photo->guessExtension();

                $photo->move(
                    $this->getParameter('kernel.project_dir').'/public/img/pfp', $photoNom
                );
                $utilisateur->setPhoto('img/pfp/'.$photoNom);
            }

            $utilisateur->setRoles(['ROLE_UTILISATEUR']);

            $emi->persist($utilisateur);
            $emi->flush();
            return $this->redirectToRoute('app_accueil');
        }

        return $this->render('inscription.html.twig', ['form' => $form]);
    }
}