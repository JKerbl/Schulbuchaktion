<?php

namespace App\Repository;

use App\Entity\SchoolClass;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SchoolClass>
 *
 * @method SchoolClass|null find($id, $lockMode = null, $lockVersion = null)
 * @method SchoolClass|null findOneBy(array $criteria, array $orderBy = null)
 * @method SchoolClass[]    findAll()
 * @method SchoolClass[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SchoolClassRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SchoolClass::class);
    }

    public function findAllByDepartmentID(int $departmentId): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.department = :departmentId')
            ->setParameter('departmentId', $departmentId)
            ->getQuery()
            ->getResult();
    }

    public function findAlLByYear(int $year): array
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.year = :year')
            ->setParameter('year', $year)
            ->getQuery()
            ->getResult();
    }

    public function findAllByYearAndDepartment(int $year, int $department): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.year = :year')
            ->andWhere('s.department = :department')
            ->setParameter('year', $year)
            ->setParameter('department', $department)
            ->getQuery()
            ->getResult();
    }

    public function findAllYears(): ?array
    {
        $result =  $this->createQueryBuilder('s')
            ->select('s.year')
            ->distinct()
            ->getQuery()
            ->getResult();

        return array_map('current', $result);
    }

    public function findHighestYear(): ?int
    {
        return $this->createQueryBuilder('s')
            ->select('MAX(s.year) as year')
            ->getQuery()
            ->getSingleScalarResult();
    }

    //    /**
    //     * @return SchoolClass[] Returns an array of SchoolClass objects
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

    //    public function findOneBySomeField($value): ?SchoolClass
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
