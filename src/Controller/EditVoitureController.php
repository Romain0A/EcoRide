<?php

namespace App\Controller;

use App\Entity\Voiture;
use App\Form\VoitureType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/mes_voitures')]
final class EditVoitureController extends AbstractController
{

    #[Route('', name: 'app_voiture')]
    public function index(): Response
    {
        $user = $this->getUser();

        return $this->render('voiture.html.twig', ['voitures' => $user->getVoitures(),]);
    }

    #[Route('/ajouter', name: 'app_ajouter_voiture')]
    public function ajouter(Request $request,EntityManagerInterface $emi): Response {
        $voiture = new Voiture();
        $form = $this->createForm(VoitureType::class, $voiture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $voiture->setUtilisateur($this->getUser());
            $emi->persist($voiture);
            $emi->flush();

            return $this->redirectToRoute('app_voiture');
        }

        return $this->render('editVoiture.html.twig', ['form' => $form->createView(),]);
    }

    #[Route('/edit/{id}', name: 'app_voiture_edit')]
    public function edit(Voiture $voiture,Request $request,EntityManagerInterface $emi): Response {
        if ($voiture->getUtilisateur() !== $this->getUser()) {
            return $this->redirectToRoute('app_voiture');
        }

        $form = $this->createForm(VoitureType::class, $voiture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $emi->flush();
            return $this->redirectToRoute('app_voiture');
        }

        return $this->render('editVoiture.html.twig', ['form' => $form->createView(),]);
    }

    #[Route('/supprimer/{id}', name: 'app_supprimer_voiture')]
    public function supprimer(Voiture $voiture,EntityManagerInterface $emi): Response {
        if ($voiture->getUtilisateur() !== $this->getUser()) {
            return $this->redirectToRoute('app_voiture');
        }

        $emi->remove($voiture);
        $emi->flush();

        return $this->redirectToRoute('app_voiture');
    }
}
