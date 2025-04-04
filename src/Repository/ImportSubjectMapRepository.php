<?php

namespace App\Repository;

use App\Entity\ImportSubjectMap;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ImportSubjectMap>
 *
 * @method ImportSubjectMap|null find($id, $lockMode = null, $lockVersion = null)
 * @method ImportSubjectMap|null findOneBy(array $criteria, array $orderBy = null)
 * @method ImportSubjectMap[]    findAll()
 * @method ImportSubjectMap[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImportSubjectMapRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ImportSubjectMap::class);
    }

    public function findForSubjectOrSearch(String $subject = null, String $search = null): array
    {
        $qb = $this->createQueryBuilder('i');

        if ($subject) {
            if ($subject == 'n'){
                $qb->andWhere('i.subject is NULL');
            } else {
                $qb->andWhere('i.subject = :subject')
                    ->setParameter('subject', $subject);
            }
        }

        if ($search) {
            $qb->andWhere('i.name LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }

        return $qb->getQuery()->getResult();
    }

    //    /**
    //     * @return ImportSubjectMap[] Returns an array of ImportSubjectMap objects
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

    //    public function findOneBySomeField($value): ?ImportSubjectMap
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
