<?php

namespace App\Controller;

use App\Entity\Covoiturage;
use App\Entity\CovoiturageParticipant;
use App\Repository\CovoiturageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DetailController extends AbstractController
{
    #[Route('/detail', name: 'app_detail')]
    public function index(Request $request,CovoiturageRepository $covoiturageRepository): Response
    {
        if(!$_GET["id"]){
            return $this->redirectToRoute('app_covoiturages');
        }

        $covoiturage = $covoiturageRepository->findOneById($_GET["id"]);

        return $this->render('detail.html.twig',[ 'covoiturage' => $covoiturage]);
    }

    #[Route('participer/{id}', name: 'app_participer')]
    public function participer(Covoiturage $covoiturage,EntityManagerInterface $em): Response {

        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        if ($covoiturage->getChauffeur() === $user) {
            throw $this->createAccessDeniedException();
        }
        if ($covoiturage->getNbPlace() <= 0) {
            $this->addFlash('error', 'Plus de place disponible.');
            return $this->redirectToRoute('app_detail', ['id' => $covoiturage->getId()]);
        }
        if ($covoiturage->getStatut() != "planifier") {
            $this->addFlash('error', 'Ce Covoiturage à déjà commencer.');
            return $this->redirectToRoute('app_detail', ['id' => $covoiturage->getId()]);
        }

        $participant = false;
        foreach ($covoiturage->getCovoiturageParticipant() as $participation) {
            if ($participation->getPassager() === $user) {
                $participant = true;
                break;
            }
        }

        if ($participant) {
            $this->addFlash('error', 'Vous participez déjà à ce covoiturage.');
            return $this->redirectToRoute('app_detail', ['id' => $covoiturage->getId()]);
        }
        if ($user->getCredit() < $covoiturage->getPrixPersonne()) {
            $this->addFlash('error', 'Crédit insuffisant.');
            return $this->redirectToRoute('app_detail', ['id' => $covoiturage->getId()]);
        }

        $participation = new CovoiturageParticipant;
        $participation->setCovoiturage($covoiturage);
        $participation->setPassager($user);
        $participation->setConfirme(false);
        $em->persist($participation);
        $user->setCredit($user->getCredit() - $covoiturage->getPrixPersonne());
        $covoiturage->setNbPlace($covoiturage->getNbPlace() - 1);

        $em->flush();

        return $this->redirectToRoute('app_espace_utilisateur');
    }
}
