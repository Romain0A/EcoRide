<?php

namespace App\Repository;

use App\Entity\CovoiturageParticipant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CovoiturageParticipant>
 */
class CovoiturageParticipantRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CovoiturageParticipant::class);
    }

    public function creditsByMonth(): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT MONTH(c.date_depart) as month, COUNT(copa.covoiturage_id) * 2 as total FROM covoiturage_participant copa JOIN covoiturage c ON copa.covoiturage_id = c.id WHERE copa.confirme = 1 GROUP BY MONTH(c.date_depart) ORDER BY MONTH(c.date_depart)";
        return $conn->executeQuery($sql)->fetchAllAssociative();
    }

    public function countCurrentMonthByDay(): array{
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT DAY(c.date_depart) as day, COUNT(copa.covoiturage_id) * 2 as total FROM covoiturage_participant copa JOIN covoiturage c ON copa.covoiturage_id = c.id WHERE copa.confirme = 1 AND MONTH(c.date_depart) = MONTH(CURRENT_DATE()) AND YEAR(c.date_depart) = YEAR(CURRENT_DATE()) GROUP BY DAY(c.date_depart) ORDER BY DAY(c.date_depart)";
        return $conn->executeQuery($sql)->fetchAllAssociative();
    }

    //    /**
    //     * @return CovoiturageParticipant[] Returns an array of CovoiturageParticipant objects
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

    //    public function findOneBySomeField($value): ?CovoiturageParticipant
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
