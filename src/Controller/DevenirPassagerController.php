<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DevenirPassagerController extends AbstractController
{
    #[Route('/devenir_passager', name: 'app_devenir_passager')]
    public function index(EntityManagerInterface $emi): Response
    {
        if (!$this->getUser() || $this->isGranted('ROLE_PASSAGER')) {
            return $this->redirectToRoute('app_accueil');
        }

        $user = $this->getUser();
        $roles = $user->getRoles();
        $roles[] = 'ROLE_PASSAGER';
        $user->setRoles($roles);
        $emi->flush();

        return $this->redirectToRoute('app_espace_utilisateur');
    }
}
