<?php

namespace App\Controller;

use App\Form\ChangePasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

final class ChangePasswordController extends AbstractController
{
    #[Route('/changer_password', name: 'app_changer_password')]
    public function index(Request $request,EntityManagerInterface $em,UserPasswordHasherInterface $passwordHasher): Response
    {
        $form = $this->createForm(ChangePasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user = $this->getUser();

            $newPassword = $form->get('newPassword')->getData();

            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $newPassword
            );

            $user->setPassword($hashedPassword);
            $em->flush();

            return $this->redirectToRoute('app_espace_utilisateur');
        }

        return $this->render('changePassword.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
