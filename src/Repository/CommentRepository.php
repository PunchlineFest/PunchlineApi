<?php

namespace App\Repository;

use App\Entity\Comment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Comment>
 */
class CommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    public function findByAndFilter($criteria, $orderBy): array
    {
        $query = $this->createQueryBuilder('c');

        if (\array_key_exists('eventId', $criteria)) {
            $query->leftJoin('c.event', 'event') // Joindre la relation artiste
            ->addSelect('event')
                ->andWhere('event.id = :eventId')
                ->setParameter('eventId', $criteria['eventId']);
        }
        unset($criteria['eventId']);

        foreach ($criteria as $key => $value) {
            $query->andWhere("c.$key = :val")
                ->setParameter('val', $value);
        }

        foreach ($orderBy as $property => $order) {
            $query->orderBy("c.$property", $order);
        }

        return $query->getQuery()
            ->getResult();
    }

    //    public function findOneBySomeField($value): ?Comment
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
