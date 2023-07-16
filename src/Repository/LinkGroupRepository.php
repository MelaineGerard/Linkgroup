<?php

namespace App\Repository;

use App\Entity\LinkGroup;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LinkGroup>
 *
 * @method LinkGroup|null find($id, $lockMode = null, $lockVersion = null)
 * @method LinkGroup|null findOneBy(array $criteria, array $orderBy = null)
 * @method LinkGroup[]    findAll()
 * @method LinkGroup[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LinkGroupRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LinkGroup::class);
    }

//    /**
//     * @return LinkGroup[] Returns an array of LinkGroup objects
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

//    public function findOneBySomeField($value): ?LinkGroup
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
