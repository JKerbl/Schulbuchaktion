<?php

namespace App\Repository;

use App\Entity\SchoolGrades;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SchoolGrades>
 *
 * @method SchoolGrades|null find($id, $lockMode = null, $lockVersion = null)
 * @method SchoolGrades|null findOneBy(array $criteria, array $orderBy = null)
 * @method SchoolGrades[]    findAll()
 * @method SchoolGrades[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SchoolGradesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SchoolGrades::class);
    }

    //    /**
    //     * @return SchoolGrades[] Returns an array of SchoolGrades objects
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

    //    public function findOneBySomeField($value): ?SchoolGrades
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
