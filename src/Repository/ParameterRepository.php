<?php

namespace App\Repository;

use App\Entity\Parameter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Parameter>
 *
 * @method Parameter|null find($id, $lockMode = null, $lockVersion = null)
 * @method Parameter|null findOneBy(array $criteria, array $orderBy = null)
 * @method Parameter[]    findAll()
 * @method Parameter[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ParameterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Parameter::class);
    }

    public function findHighestYear(): ?int
    {
        return $this->createQueryBuilder('p')
            ->select('MAX(p.year)')
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    public function findWhereYearNotNull()
    {
        return $this->createQueryBuilder('p')
            ->where('p.year IS NOT NULL')
            ->getQuery()
            ->getResult();
    }

    public function findAllYears(): ?array
    {
        $result = $this->createQueryBuilder('s')
            ->select('s.year')
            ->where('s.year IS NOT NULL')
            ->distinct()
            ->getQuery()
            ->getResult();

        return array_map('current', $result);
    }

    public function findAllByYear(int $year): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.year = :year')
            ->setParameter('year', $year)
            ->getQuery()
            ->getResult()
        ;
    }

    //    /**
    //     * @return Parameter[] Returns an array of Parameter objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Parameter
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
