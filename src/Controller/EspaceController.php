<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class EspaceController extends AbstractController
{
    #[Route('/espace', name: 'app_espace')]
    public function index(): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        if($this->isGranted('ROLE_ADMIN')){
            return $this->redirectToRoute('app_espace_administrateur');
        }

        if($this->isGranted('ROLE_EMPLOYE')){
            return $this->redirectToRoute('app_espace_employe');
        }

        return $this->redirectToRoute('app_espace_utilisateur');
    }
}
