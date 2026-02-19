<?php

namespace App\Controller;

use App\Entity\Covoiturage;
use App\Form\CovoiturageFiltreType;
use App\Repository\CovoiturageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;

final class CovoituragesController extends AbstractController
{
    #[Route('/covoiturages', name: 'app_covoiturages')]
    public function index(Request $request,CovoiturageRepository $covoiturageRepository): Response
    {
        $form = $this->createForm(CovoiturageFiltreType::class);
        $form->handleRequest($request);
        $data = $form->isSubmitted() ? $form->getData() : [];
        $covoiturages = $covoiturageRepository->search($data);

        return $this->render('covoiturages.html.twig', ['covoiturages' => $covoiturages, 'form' => $form->createView()]);
    }

    #[Route('/covoiturage/{id}/demarrer', name: 'app_covoiturage_demarrer')]
    public function demarrer(Covoiturage $covoiturage,EntityManagerInterface $emi): Response {

        if ($covoiturage->getChauffeur() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $covoiturage->setStatut("demarrer");
        $covoiturage->setDebutTrajet(new \DateTime());

        $emi->flush();

        return $this->redirectToRoute('app_espace_utilisateur');
    }

    #[Route('/covoiturage/{id}/terminer', name: 'app_covoiturage_terminer')]
    public function terminer(Covoiturage $covoiturage,EntityManagerInterface $emi): Response {

        if ($covoiturage->getChauffeur() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $covoiturage->setStatut("terminer");
        $covoiturage->setFinTrajet(new \DateTime());
        $emi->flush();

        $mailer = new MailerInterface;

        foreach ($covoiturage->getCovoiturageParticipant() as $participant) {
            $email = (new Email())
            ->from('noreply@ecoride.fr')
            ->to($participant->getEmail())
            ->subject('Confirmation de trajet')
            ->text('Merci de confirmer votre trajet sur votre espace utilisateur.');

            $mailer->send($email);
        }

        return $this->redirectToRoute('app_espace_utilisateur');
    }
}
