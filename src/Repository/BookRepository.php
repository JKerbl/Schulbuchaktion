<?php

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

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

    public function getPaginatedEntries(int $limit, int $currentPage, string $search = null): array
    {
        $offset = ($currentPage - 1) * $limit;

        if ($offset < 1) $offset = 1;

        $queryBuilder = $this->createQueryBuilder('b')
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        if ($search) {
            $queryBuilder
                ->where('b.title LIKE :search')
                ->orWhere('b.bnr LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }

        return $queryBuilder->getQuery()->getResult();
    }

    public function getTotalEntries(string $search = null): int
    {
        $queryBuilder = $this->createQueryBuilder('b')
            ->select('count(b.id)');

        if ($search) {
            $queryBuilder->where('b.title LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }

        return (int) $queryBuilder->getQuery()->getSingleScalarResult();
    }

}
