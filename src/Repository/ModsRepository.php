<?php

namespace App\Repository;

use App\Entity\Mods;
use App\Entity\Server;
use App\UniqueNameInterface\ModsInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Mods>
 *
 * @method Mods|null find($id, $lockMode = null, $lockVersion = null)
 * @method Mods|null findOneBy(array $criteria, array $orderBy = null)
 * @method Mods[]    findAll()
 * @method Mods[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ModsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Mods::class);
    }

    public function getMods (Server $server): array {
        $id = ModsInterface::ENTITY_ID;
        $name = ModsInterface::ENTITY_NAME;
        $externalId = ModsInterface::ENTITY_EXTERNALID;

        return $this->createQueryBuilder('m')
            ->select("m.$id, m.$name, m.$externalId")
            ->where('m.server = :server_id')
            ->setParameter('server_id', $server->getId())
            ->getQuery()
            ->getArrayResult()
        ;
    }

//    /**
//     * @return Mods[] Returns an array of Mods objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Mods
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
