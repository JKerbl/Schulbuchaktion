<?php

namespace App\Repository;

use App\Entity\BookOrder;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use phpDocumentor\Reflection\Types\Integer;

/**
 * @extends ServiceEntityRepository<BookOrder>
 *
 * @method BookOrder|null find($id, $lockMode = null, $lockVersion = null)
 * @method BookOrder|null findOneBy(array $criteria, array $orderBy = null)
 * @method BookOrder[]    findAll()
 * @method BookOrder[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookOrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BookOrder::class);
    }

    public function findOrdersByYear(int $year): array
    {
        return $this->createQueryBuilder('o')
            ->innerJoin('o.book', 'b')
            ->andWhere('b.year = :year')
            ->setParameter('year', $year)
            ->getQuery()
            ->getResult();
    }

    public function findOrdersByYearAndDepartment(int $year, int $departmentId): array
    {
        return $this->createQueryBuilder('o')
            ->innerJoin('o.book', 'b')
            ->innerJoin('o.schoolclass', 'c')
            ->andWhere('c.year = :year')
            ->andWhere('c.department = :departmentId')
            ->setParameter('year', $year)
            ->setParameter('departmentId', $departmentId)
            ->select('o')
            ->getQuery()
            ->getResult();
    }

    public function findOrdersByYearAndGrade(int $year, int $grade): array
    {
        return $this->createQueryBuilder('o')
            ->innerJoin('o.book', 'b')
            ->innerJoin('o.schoolclass', 'c')
            ->andWhere('c.year = :year')
            ->andWhere('c.grade = :grade')
            ->setParameter('year', $year)
            ->setParameter('grade', $grade)
            ->select('o')
            ->getQuery()
            ->getResult();
    }

    public function findOrdersByYearAndDepartmentAndGrade(int $year, int $departmentId, int $grade): array
    {
        return $this->createQueryBuilder('o')
            ->innerJoin('o.book', 'b')
            ->innerJoin('o.schoolclass', 'c')
            ->andWhere('b.year = :year')
            ->andWhere('c.department = :departmentId')
            ->andWhere('c.grade = :grade')
            ->setParameter('year', $year)
            ->setParameter('departmentId', $departmentId)
            ->setParameter('grade', $grade)
            ->select('o')
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return BookOrder[] Returns an array of BookOrder objects
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

    //    public function findOneBySomeField($value): ?BookOrder
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function getOrdersOfDepartment($departmentId): array
    {
        return $this->createQueryBuilder('bo')
            ->join('bo.schoolclass', 'sc')
            ->join('sc.department', 'd')
            ->andWhere('d.id = :departmentId')
            ->setParameter('departmentId', $departmentId)
            ->getQuery()
            ->getResult();
    }
}
