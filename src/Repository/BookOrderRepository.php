<?php

namespace App\Repository;

use App\Entity\BookOrder;
use App\Entity\Subject;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use phpDocumentor\Reflection\DocBlock\Tags\Source;
use phpDocumentor\Reflection\Types\Integer;
use PhpParser\Node\Expr\Cast\Double;
use Symfony\Config\TwigExtra\StringConfig;

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
            ->innerJoin('o.schoolclass', 'c')
            ->andWhere('c.year = :year')
            ->setParameter('year', $year)
            ->getQuery()
            ->getResult();
    }

    public function findBnrCountsByYear(int $year): array
    {
        return $this->createQueryBuilder('o')
            ->select('b.bnr, COUNT(o.id) as orderCount')
            ->innerJoin('o.book', 'b')
            ->andWhere('b.year = :year')
            ->setParameter('year', $year)
            ->groupBy('b.bnr')
            ->getQuery()
            ->getResult();
    }

    public function getTotalEntries(int $year, int $departmentId = null, int $grade = null, int $subject = null, string $search = null): int
    {
        $query = $this->createQueryBuilder('o')
            ->select('COUNT(o.id)')
            ->innerJoin('o.schoolclass', 'c')
            ->innerJoin('c.department', 'd')
            ->innerJoin('o.book', 'b')
            ->andWhere('c.year = :year')
            ->setParameter('year', $year);

        if ($search) {
            $query->andWhere('(b.title LIKE :search OR b.bnr LIKE :search2)')
                ->setParameter('search', '%' . $search . '%')
                ->setParameter('search2', '%' . $search . '%');
        }

        if ($departmentId) {
            $query->andWhere('d.id = :departmentId')
                ->setParameter('departmentId', $departmentId);
        }

        if ($grade) {
            $query->andWhere('c.grade = :grade')
                ->setParameter('grade', $grade);
        }

        if ($subject) {

            $query
                ->join('o.subject', 's')  // ✅ Direkt `book_order.subject_id` mit `subject.id` verbinden
                ->andWhere('s.id = :subjectId')
                ->setParameter('subjectId', $subject);
            /* $query
                ->join('b.importSubjectMap', 'ism')
                ->join('ism.subject', 's')
                ->andWhere('s.id = :subjectId')
                ->setParameter('subjectId', $subject);

            $subject = $this->getEntityManager()->getRepository(Subject::class)->find($subject);
            if ($subject && stripos($subject->getFullName(), 'F') === 0) {
                $query
                    ->andWhere('s.fullName LIKE :subject')
                    ->setParameter('subject', 'F%');
            }*/
        }

        return $query->getQuery()->getSingleScalarResult();
    }

    public function getPaginatedEntries(int $limit, int $currentPage, string $year, int $departmentId = null, int $grade = null, int $subject = null, String $search = null, String $sortDirection = null, String $sortBy = null): array
    {
        $offset = ($currentPage - 1) * $limit;

        if ($offset < 1) $offset = 0;

        $queryBuilder = $this->createQueryBuilder('o')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->innerJoin('o.schoolclass', 'c')
            ->andWhere('c.year = :year')
            ->setParameter('year', $year);

        if ($search || ($sortDirection && $sortBy)) {
            $queryBuilder->innerJoin('o.book', 'b');

            if ($search != null) {
                $queryBuilder->andWhere('(b.title LIKE :search OR b.bnr LIKE :search2)')
                    ->setParameter('search', '%' . $search . '%')
                    ->setParameter('search2', '%' . $search . '%');
            }

            if ($sortBy && $sortDirection) {
                if ($sortBy === 'bnr'){
                    $queryBuilder->addOrderBy('b.bnr', $sortDirection);
                    $queryBuilder->addOrderBy('c.name', "ASC");
                } else if ($sortBy === 'class'){
                    $queryBuilder->orderBy('c.name', $sortDirection);
                }
            }

            if ($subject) {
                $queryBuilder
                    ->join('o.subject', 's')  // ✅ Direkt `book_order.subject_id` mit `subject.id` verbinden
                    ->andWhere('s.id = :subjectId')
                    ->setParameter('subjectId', $subject);
                /*$queryBuilder
                    ->join('b.importSubjectMap', 'ism')
                    ->join('ism.subject', 's')
                    ->andWhere('s.id = :subjectId')
                    ->setParameter('subjectId', $subject);

               $subject = $this->getEntityManager()->getRepository(Subject::class)->find($subject);
                if ($subject && stripos($subject->getFullName(), 'F') === 0) {
                    $queryBuilder
                        ->andWhere('s.fullName LIKE :subject')
                        ->setParameter('subject', 'F%');
                }*/
            }
        }

        if ($departmentId) {
            $queryBuilder->innerJoin('c.department', 'd')
                ->andWhere('d.id = :departmentId')
                ->setParameter('departmentId', $departmentId);
        }

        if ($grade) {
            $queryBuilder->andWhere('c.grade = :grade')
                ->setParameter('grade', $grade);
        }

        return $queryBuilder->getQuery()->getResult();
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

    public function getBudgetForFilters(string $year, int $departmentId = null, int $grade = null, int $subject = null, string $search = null): float
        {
            $queryBuilder = $this->createQueryBuilder('bo')
                ->select('SUM(b.price * sc.studentsAmount)')
                ->join('bo.schoolclass', 'sc')
                ->innerJoin('bo.book', 'b')
                ->andWhere('sc.year = :year')
                ->setParameter('year', $year);

            if ($search) {
                $queryBuilder->andWhere('(b.title LIKE :search OR b.bnr LIKE :search2)')
                    ->setParameter('search', '%' . $search . '%')
                    ->setParameter('search2', '%' . $search . '%');
            }

            if ($subject) {
                $queryBuilder
                    ->join('bo.subject', 's')
                    ->andWhere('s.id = :subjectId')
                    ->setParameter('subjectId', $subject);
            }

            if ($departmentId) {
                $queryBuilder->innerJoin('sc.department', 'd')
                    ->andWhere('d.id = :departmentId')
                    ->setParameter('departmentId', $departmentId);
            }

            if ($grade) {
                $queryBuilder->andWhere('sc.grade = :grade')
                    ->setParameter('grade', $grade);
            }

            return (float) $queryBuilder->getQuery()->getSingleScalarResult();
        }
}
