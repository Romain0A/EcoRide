<?php

namespace App\Repository;

use App\Entity\Covoiturage;
use App\Entity\Utilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Covoiturage>
 */
class CovoiturageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Covoiturage::class);
    }

    public function search(array $data)
{
    $qb = $this->createQueryBuilder('covoiturage')->leftJoin('covoiturage.voiture', 'voiture')->addSelect('voiture');

    if (!empty($data['lieu_depart'])) {
        $qb->andWhere('covoiturage.lieu_depart LIKE :depart')->setParameter('depart', '%'.$data['lieu_depart'].'%');
    }

    if (!empty($data['lieu_arrivee'])) {
        $qb->andWhere('covoiturage.lieu_arrivee LIKE :arrivee')->setParameter('arrivee', '%'.$data['lieu_arrivee'].'%');
    }

    if (!empty($data['date_depart'])) {
        $qb->andWhere('covoiturage.date_depart = :date')->setParameter('date', $data['date_depart']);
    }

    if (!empty($data['prix_max'])) {
        $qb->andWhere('covoiturage.prix_personne <= :prix')->setParameter('prix', $data['prix_max']);
    }

    if (!empty($data['ecologique'])) {
        $qb->andWhere('voiture.energie = :energie')->setParameter('energie', 'electrique');
    }

    return $qb->getQuery()->getResult();
}

public function findOneById($id): ?Covoiturage
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.id = :val')
            ->setParameter('val', $id)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findByUtilisateur(Utilisateur $utilisateur): array
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.CovoiturageParticipant', 'copa')
            ->leftJoin('copa.passager', 'p')
            ->Where('c.chauffeur = :val')
            ->orWhere('p = :val')
            ->setParameter('val', $utilisateur)
            ->orderBy('c.date_depart', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function countByMonth(): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT MONTH(date_depart) as month, COUNT(id) as total FROM covoiturage GROUP BY MONTH(date_depart) ORDER BY MONTH(date_depart)";
        return $conn->executeQuery($sql)->fetchAllAssociative();
    }

    public function countCurrentMonthByDay(): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT DAY(date_depart) as day, COUNT(id) as total FROM covoiturage WHERE MONTH(date_depart) = MONTH(CURRENT_DATE()) AND YEAR(date_depart) = YEAR(CURRENT_DATE()) GROUP BY DAY(date_depart) ORDER BY DAY(date_depart)";
        return $conn->executeQuery($sql)->fetchAllAssociative();
    }


//    /**
//     * @return Covoiturage[] Returns an array of Covoiturage objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Covoiturage
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
