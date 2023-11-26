<?php

namespace App\Repository;

use App\Entity\Config;
use App\UniqueNameInterface\ConfigInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Config>
 *
 * @method Config|null find($id, $lockMode = null, $lockVersion = null)
 * @method Config|null findOneBy(array $criteria, array $orderBy = null)
 * @method Config[]    findAll()
 * @method Config[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConfigRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Config::class);
    }

    public function getPorts () :array {
        $port = ConfigInterface::ENTITY_PORT;

        return $this->createQueryBuilder('n')
            ->select("n.$port")
            ->getQuery()
            ->getArrayResult()
        ;
    }
}
