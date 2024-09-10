<?php

namespace App\Repository;

use App\Entity\Artist;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Artist>
 */
class ArtistRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Artist::class);
    }

        /**
         * @return Artist[] Returns an array of Artist objects
         */
    public function findByAndFilter($criteria, $orderBy): array
    {
        $query = $this->createQueryBuilder('a');

        foreach ($criteria as $key => $value) {
            $query->andWhere("a.$key = :val")
                ->setParameter('val', $value);
        }

        foreach ($orderBy as $property => $order) {
            $query->orderBy("a.$property", $order);
        }

        return $query->getQuery()
            ->getResult();
    }

    //    public function findOneBySomeField($value): ?Artist
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
