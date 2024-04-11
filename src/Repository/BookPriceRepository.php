<?php

namespace App\Repository;

use App\Entity\BookPrice;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BookPrice>
 *
 * @method BookPrice|null find($id, $lockMode = null, $lockVersion = null)
 * @method BookPrice|null findOneBy(array $criteria, array $orderBy = null)
 * @method BookPrice[]    findAll()
 * @method BookPrice[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookPriceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BookPrice::class);
    }

    //    /**
    //     * @return BookPrice[] Returns an array of BookPrice objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('b.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?BookPrice
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
