<?php

namespace App\Repository;

use App\Entity\Server;
use App\UniqueNameInterface\ServerInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Server>
 *
 * @method Server|null find($id, $lockMode = null, $lockVersion = null)
 * @method Server|null findOneBy(array $criteria, array $orderBy = null)
 * @method Server[]    findAll()
 * @method Server[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ServerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Server::class);
    }

//    /**
//     * @return Server[] Returns an array of Server objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

    public function findOnlineOlderThan7Days () {
        $qb = $this->createQueryBuilder('e');

        // Calculate the date 7 days ago
        $sevenDaysAgo = (new \DateTime())->modify('-7 days')->format('Y-m-d');

        return $qb->where('e.status = :status')
            ->andWhere('e.modified_at <= :sevenDaysAgo')
            ->setParameter('status', 'online')
            ->setParameter('sevenDaysAgo', $sevenDaysAgo)
            ->getQuery()
            ->getResult();
    }
}
