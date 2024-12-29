<?php

    namespace App\Repository;

    use App\Entity\Login;
    use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
    use Doctrine\Persistence\ManagerRegistry;
    use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
    use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
    use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

    /**
     * @extends ServiceEntityRepository<Login>
     * @implements PasswordUpgraderInterface<Login>
     *
     * @method Login|null find($id, $lockMode = null, $lockVersion = null)
     * @method Login|null findOneBy(array $criteria, array $orderBy = null)
     * @method Login[]    findAll()
     * @method Login[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
     */
    class LoginRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
    {
        public function __construct(ManagerRegistry $registry)
        {
            parent::__construct($registry, Login::class);
        }

        /**
         * Used to upgrade (rehash) the user's password automatically over time.
         */
        public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
        {
            if (!$user instanceof Login) {
                throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
            }

            $user->setPassword($newHashedPassword);
            $this->getEntityManager()->persist($user);
            $this->getEntityManager()->flush();
        }

//    /**
//     * @return Login[] Returns an array of Login objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('l.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Login
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
    }
