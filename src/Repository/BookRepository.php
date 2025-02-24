<?php

namespace App\Repository;

use App\Entity\Book;
use App\Entity\Subject;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use phpDocumentor\Reflection\Types\Integer;

/**
 * @extends ServiceEntityRepository<Book>
 *
 * @method Book|null find($id, $lockMode = null, $lockVersion = null)
 * @method Book|null findOneBy(array $criteria, array $orderBy = null)
 * @method Book[]    findAll()
 * @method Book[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    public function getAllYears(): array
    {
        $result = $this->createQueryBuilder('b')
            ->select('b.year')
            ->distinct()
            ->orderBy('b.year', 'ASC')
            ->getQuery()
            ->getResult();

        return array_map('current', $result);
    }

    public function findByBNRAndYear(int $bnr, int $year): ?Book {
        return $this->createQueryBuilder('b')
            ->andWhere('b.bnr = :bnr')
            ->setParameter('bnr', $bnr)
            ->andWhere('b.year = :year')
            ->setParameter('year', $year)
            ->getQuery()
            ->getOneOrNullResult();
    }


    //    /**
    //     * @return Book[] Returns an array of Book objects
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

    //    public function findOneBySomeField($value): ?Book
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function getPaginatedEntries(int $limit, int $currentPage, string $year, int $subjectId = null, int $grade = null, string $search = null): array
    {
        $offset = ($currentPage - 1) * $limit;

        if ($offset < 1) $offset = 0;

        $queryBuilder = $this->createQueryBuilder('b')
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        if ($search) {
            $queryBuilder
                ->where('b.title LIKE :search')
                ->orWhere('b.bnr LIKE :search')
                ->orWhere('b.shortTitle LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }

        if ($year) {
            $queryBuilder
                ->andWhere('b.year = :year')
                ->setParameter('year', $year);
        }

        if ($subjectId) {
            $queryBuilder
                ->join('b.importSubjectMap', 'ism')
                ->join('ism.subject', 's')
                ->andWhere('s.id = :subjectId')
                ->setParameter('subjectId', $subjectId);

            $subject = $this->getEntityManager()->getRepository(Subject::class)->find($subjectId);
            if ($subject && stripos($subject->getFullName(), 'F') === 0) {
                $queryBuilder
                    ->andWhere('s.fullName LIKE :subject')
                    ->setParameter('subject', 'F%');
            }
        }

        if ($grade) {
            $queryBuilder
                ->andWhere('b.schoolGrades LIKE :grade')
                ->setParameter('grade', '%' . $grade . '%');
        }

        return $queryBuilder->getQuery()->getResult();
    }

    public function getTotalEntries(int $year, int $subjectId = null, int $grade = null, string $search = null): int
    {
        $queryBuilder = $this->createQueryBuilder('b')
            ->select('count(b.id)')
            ->where('b.year = :year')
            ->setParameter('year', $year);

        if ($search) {
            $queryBuilder->andWhere('b.title LIKE :search')
                ->setParameter('search', '%' . $search . '%')
                ->orWhere('b.bnr LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }

        if ($subjectId) {
            $queryBuilder
                ->join('b.importSubjectMap', 'ism')
                ->join('ism.subject', 's')
                ->andWhere('s.id = :subjectId')
                ->setParameter('subjectId', $subjectId);

            $subject = $this->getEntityManager()->getRepository(Subject::class)->find($subjectId);
            if ($subject && stripos($subject->getFullName(), 'F') === 0) {
                $queryBuilder
                    ->andWhere('s.fullName LIKE :subject')
                    ->setParameter('subject', 'F%');
            }
        }

        if ($grade) {
            $queryBuilder
                ->andWhere('b.schoolGrades LIKE :grade')
                ->setParameter('grade', '%' . $grade . '%');
        }

        return (int)$queryBuilder->getQuery()->getSingleScalarResult();
    }

}
