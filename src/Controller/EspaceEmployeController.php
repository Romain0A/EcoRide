<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Repository\AvisRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class EspaceEmployeController extends AbstractController
{
    #[Route('/espace_employe', name: 'app_espace_employe')]
    public function index(AvisRepository $avisRepository): Response
    {
        if(!$this->isGranted('ROLE_EMPLOYE')){
            return $this->redirectToRoute('app_login');
        }

        $avisEnAttente = $avisRepository->findBy([
            'statut' => 'en_attente'
        ]);

        $malPasse = $avisRepository->findBy([
            'statut' => 'malpasse'
        ]);

        return $this->render('espaceEmploye.html.twig', [
            'avis' => $avisEnAttente,
            'malPasse' => $malPasse,
        ]);
    }

    #[Route('/avis/accepter/{id}', name: 'app_avis_accepter')]
    public function accepter(Avis $avis, EntityManagerInterface $em): Response
    {
        if (!$this->isGranted('ROLE_EMPLOYE')) {
            throw $this->createAccessDeniedException();
        }

        $avis->setStatut('valide');
        $em->flush();

        return $this->redirectToRoute('app_espace_employe');
    }

    #[Route('/avis/refuser/{id}', name: 'app_avis_refuser')]
    public function refuser(Avis $avis, EntityManagerInterface $em): Response
    {
        if (!$this->isGranted('ROLE_EMPLOYE')) {
            throw $this->createAccessDeniedException();
        }

        $avis->setStatut('refuse');
        $em->flush();

        return $this->redirectToRoute('app_espace_employe');
    }

    #[Route('/malpasse/chauffeur/{id}', name: 'app_malpasse_chauffeur')]
    public function payerChauffeur(Avis $avis, EntityManagerInterface $em): Response
    {
        if (!$this->isGranted('ROLE_EMPLOYE')) {
            throw $this->createAccessDeniedException();
        }

        $covoiturage = $avis->getCovoiturage();
        $chauffeur = $avis->getReceveur();
        $prix = $covoiturage->getPrixPersonne();

        $chauffeur->setCredit($chauffeur->getCredit() + ($prix - 2));

        $avis->setStatut('resolu_chauffeur');

        $em->flush();

        return $this->redirectToRoute('app_espace_employe');
    }

    #[Route('/malpasse/passager/{id}', name: 'app_malpasse_passager')]
    public function rembourserPassager(Avis $avis, EntityManagerInterface $em): Response
    {
        if (!$this->isGranted('ROLE_EMPLOYE')) {
            throw $this->createAccessDeniedException();
        }

        $covoiturage = $avis->getCovoiturage();
        $passager = $avis->getPoster();
        $prix = $covoiturage->getPrixPersonne();

        $passager->setCredit($passager->getCredit() + $prix);

        $avis->setStatut('resolu_passager');

        $em->flush();

        return $this->redirectToRoute('app_espace_employe');
    }
}
