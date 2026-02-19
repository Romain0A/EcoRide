<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Entity\Covoiturage;
use App\Form\AvisType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AvisController extends AbstractController
{
    #[Route('/avis/{id}', name: 'app_avis')]
    public function index(Covoiturage $covoiturage,Request $request,EntityManagerInterface $em): Response
    {
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

        if (!$participation || !$participation->isConfirme()) {
            return $this->redirectToRoute('app_accueil');
        }

        $avisExistant = $em->getRepository(Avis::class)
        ->findOneBy([
            'covoiturage' => $covoiturage,
            'poster' => $user
        ]);

        if ($avisExistant) {
            return $this->redirectToRoute('app_espace_utilisateur');
        }

        $avis = new Avis();
        $avis->setPoster($user);
        $avis->setReceveur($covoiturage->getChauffeur());
        $avis->setCovoiturage($covoiturage);
        $avis->setStatut('en_attente');

        $form = $this->createForm(AvisType::class, $avis);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($avis);
            $em->flush();
            return $this->redirectToRoute('app_espace_utilisateur');
        }

        return $this->render('avisForm.html.twig', ['form' => $form->createView()]);
    }

    #[Route('/malpasse/{id}', name: 'app_malpasse')]
    public function malpasse(Covoiturage $covoiturage,Request $request,EntityManagerInterface $em): Response
    {
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

        if (!$participation || $participation->isConfirme()) {
            return $this->redirectToRoute('app_accueil');
        }

        $avisExistant = $em->getRepository(Avis::class)
        ->findOneBy([
            'covoiturage' => $covoiturage,
            'poster' => $user
        ]);

        if ($avisExistant) {
            return $this->redirectToRoute('app_espace_utilisateur');
        }

        $avis = new Avis();
        $avis->setPoster($user);
        $avis->setReceveur($covoiturage->getChauffeur());
        $avis->setCovoiturage($covoiturage);
        $avis->setNote(0);
        $avis->setStatut('malpasse');

        $form = $this->createForm(AvisType::class, $avis);
        $form->handleRequest($request);

        if ($request->isMethod('POST')) {
            $commentaire = $request->request->get('commentaire');
            $avis->setCommentaire($commentaire);
            $participation->setConfirme(true);
            $em->persist($avis);
            $em->flush();

            return $this->redirectToRoute('app_espace_utilisateur');
        }
        return $this->render('malpasseForm.html.twig', ['form' => $form->createView()]);
    }
}
