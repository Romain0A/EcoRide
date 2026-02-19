<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Repository\CovoiturageParticipantRepository;
use App\Repository\CovoiturageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

final class EspaceAdministrateurController extends AbstractController
{
    #[Route('/espace_administrateur', name: 'app_espace_administrateur')]
    public function index(ChartBuilderInterface $chartBuilder, CovoiturageRepository $covoiturageRepo,CovoiturageParticipantRepository $copaRepo): Response
    {
        if(!$this->isGranted('ROLE_ADMIN')){
            return $this->redirectToRoute('app_login');
        }

        $covoiturageData = $covoiturageRepo->countCurrentMonthByDay();
        $creditData = $copaRepo->countCurrentMonthByDay();

        for ($i = 1; $i <= 31; $i++) {
            $labels[] = $i;
            $covoiturages[$i] = 0;
            $credits[$i] = 0;
        }

        foreach ($covoiturageData as $row) {
            $covoiturages[(int)$row['day']] = (int)$row['total'];
        }

        foreach ($creditData as $row) {
            $credits[(int)$row['day']] = (int)$row['total'];
        }

        $covoiturages = array_values($covoiturages);
        $credits = array_values($credits);

        $totalCredits = array_sum($credits);

        /* 
            Creation des graphes
        */

        $grapheCovoiturage = $chartBuilder->createChart(Chart::TYPE_LINE);

        $grapheCovoiturage->setData([
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Credit', 
                    'backgroundColor' => '#000000',
                    'borderColor' => '#000000',
                    'data' => $credits
                ],
            ],
        ]);

        $grapheCredits = $chartBuilder->createChart(Chart::TYPE_LINE);

        $grapheCredits->setData([
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Covoiturage',
                    'backgroundColor' => '#000000',
                    'borderColor' => '#000000',
                    'data' => $covoiturages
                ]
            ],
        ]);

        return $this->render('espaceAdministrateur.html.twig', ['grapheCredits' => $grapheCredits, 'grapheCovoiturage' => $grapheCovoiturage,'totalCredit' => $totalCredits,]);
    }

    #[Route('/creer_employe', name: 'app_creer_employe', methods: ['POST'])]
    public function createEmploye(Request $request,UserPasswordHasherInterface $hasher,EntityManagerInterface $em): Response 
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $user = new Utilisateur();
        $user->setPseudo($request->request->get('pseudo'));
        $user->setEmail($request->request->get('email'));
        $user->setNom($request->request->get('nom'));
        $user->setPrenom($request->request->get('prenom'));
        $user->setTelephone($request->request->get('telephone'));
        $user->setAdresse($request->request->get('adresse'));
        $dateString = $request->request->get('date_naissance');
        $date = \DateTime::createFromFormat('Y-m-d', $dateString);
        $user->setDateNaissance($date);
        $user->setRoles(['ROLE_EMPLOYE']);
        $Password = bin2hex(random_bytes(5));
        $user->setPassword(
            $hasher->hashPassword($user, $Password)
        );

        $em->persist($user);
        $em->flush();

        $mailer = new MailerInterface;
        $email = (new Email())
            ->from('noreply@ecoride.fr')
            ->to($request->request->get('email'))
            ->subject('Compte employe')
            ->text('Un compte employé vous à etais créer avec le mot de passe suivant: ' + $Password);

        $mailer->send($email);

        return $this->redirectToRoute('app_espace_administrateur');
    }

    #[Route('/supprimer_compte', name: 'app_supprimer_compte')]
    public function delete(Request $request, EntityManagerInterface $em)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $user = $em->getRepository(Utilisateur::class)
            ->findOneBy(['email' => $request->request->get('email')]);

        if ($user) {
            $em->remove($user);
            $em->flush();
        }

        return $this->redirectToRoute('app_espace_administrateur');
    }
}