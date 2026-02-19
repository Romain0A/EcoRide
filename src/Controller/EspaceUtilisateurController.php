<?php

namespace App\Controller;

use App\Entity\Covoiturage;
use App\Entity\Utilisateur;
use App\Form\CovoiturageType;
use App\Form\PreferenceType;
use App\Repository\CovoiturageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class EspaceUtilisateurController extends AbstractController
{
    #[Route('/espace_utilisateur', name: 'app_espace_utilisateur')]
    public function index(CovoiturageRepository $covoiturageRepository,Request $request,EntityManagerInterface $emi): Response
    {
        if(!$this->isGranted('ROLE_UTILISATEUR')){
            return $this->redirectToRoute('app_login');
        }
        $user = $this->getUser();
        $nouveauCovoiturage = new Covoiturage();
        $covoiturages = $covoiturageRepository->findByUtilisateur($user);
        $form = $this->createForm(CovoiturageType::class, $nouveauCovoiturage, ['user' => $user,]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $nouveauCovoiturage->setChauffeur($this->getUser());
            $nouveauCovoiturage->setStatut('planifier');

            $emi->persist($nouveauCovoiturage);
            $emi->flush();

            return $this->redirectToRoute('app_espace_utilisateur');
        }

        

        return $this->render('espaceUtilisateur.html.twig', [ 'utilisateur' => $user,'form' => $form->createView(),'covoiturages' => $covoiturages]);
    }

    #[Route('/mes_preferences', name: 'app_edit_preference')]
    public function editPreference(Request $request,EntityManagerInterface $em): Response {
        $user = $this->getUser();

        $form = $this->createForm(PreferenceType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute('app_espace_utilisateur');
        }

        return $this->render('preference.html.twig', ['form' => $form->createView(),]);
    }

    #[Route('/confirmer/{id}', name: 'app_covoiturage_confirmer')]
    public function confirmer(Covoiturage $covoiturage,EntityManagerInterface $em): Response {

        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $participant = null;
        foreach ($covoiturage->getCovoiturageParticipant() as $participation) {
            if ($participation->getPassager() === $user) {
                $participant = $participation;
                break;
            }
        }

        if (!$participant) {
            return $this->redirectToRoute('app_espace_utilisateur');
        }
        if ($participation->isConfirme()){
            return $this->redirectToRoute('app_espace_utilisateur');
        }
        if ($covoiturage->getStatut() !== 'terminer') {
            return $this->redirectToRoute('app_espace_utilisateur');
        }
        foreach ($covoiturage->getAvis() as $avis) {
            if ($avis->getPoster() === $user) {
                $this->addFlash('error', 'Vous avez déjà traité ce trajet.');
                return $this->redirectToRoute('app_espace_utilisateur');
            }
        }

        $prix = $covoiturage->getPrixPersonne();
        $chauffeur = $covoiturage->getChauffeur();
        $site = $em->getRepository(Utilisateur::class)->find(1);
        $participant->setConfirme(true);

        $chauffeur->setCredit($chauffeur->getCredit() + ($prix - 2));
        $site->setCredit($site->getCredit() + 2);

        $em->flush();

        return $this->redirectToRoute('app_espace_utilisateur');
    }
}
