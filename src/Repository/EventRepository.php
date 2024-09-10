<?php

namespace App\Repository;

use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Event>
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

        /**
         * @return Event[] Returns an array of Event objects
         */
        public function findByAndFilter($criteria, $orderBy): array
        {
            $query = $this->createQueryBuilder('e');

            foreach ($criteria as $key => $value) {
                $query->andWhere("e.$key = :val")
                    ->setParameter('val', $value);
            }

            foreach ($orderBy as $property => $order) {
                $query->orderBy("e.$property", $order);
            }

            return $query->getQuery()
                ->getResult();
        }

    //    public function findOneBySomeField($value): ?Event
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
