<?php

namespace App\Controller;

use App\Form\ChauffeurType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ChauffeurInscriptionController extends AbstractController
{
    #[Route('/chauffeur_inscription', name: 'app_chauffeur_inscription')]
    public function index(EntityManagerInterface $emi,Request $request): Response
    {
        if(!$this->isGranted('ROLE_UTILISATEUR') || $this->isGranted('ROLE_CHAUFFEUR')){
            return $this->redirectToRoute('app_accueil');
        }

        $user = $this->getUser();
        $form = $this->createForm(ChauffeurType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $voiture = $form->get('voiture')->getData();
            $voiture->setUtilisateur($user);

            if ($form->get('fumeur')->getData()) {
                $preferences[] = 'Fumeur accepté';
            }else{
                $preferences[] = 'Non fumeur';
            }
            if ($form->get('animal')->getData()) {
                $preferences[] = 'Animaux accepté';
            }else{
                $preferences[] = 'Animaux refusé';
            }
            $libre = $form->get('preference_libre')->getData();
            if ($libre) {
                $preferences[] = $libre;
            }

            $user->setPreference(implode(', ', $preferences));

            $roles = $user->getRoles();
            $roles[] = 'ROLE_CHAUFFEUR';
            $user->setRoles($roles);
            
            $emi->persist($voiture);
            $emi->persist($user);
            $emi->flush();

            return $this->redirectToRoute('app_espace_utilisateur');
        }

        return $this->render('chauffeurInscription.html.twig', ['form' => $form]);
    }
}
