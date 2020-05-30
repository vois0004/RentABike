<?php

namespace App\Repository;

use App\Entity\Ride;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Ride|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ride|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ride[]    findAll()
 * @method Ride[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RideRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ride::class);
    }

    // /**
    //  * @return Ride[] Returns an array of Ride objects
    //  */

    public function findByUserAndToday(User $user)
    {
        $qb =  $this->createQueryBuilder('r')
            ->where('YEAR(r.date) = YEAR(:datecourant)')
            ->andWhere('MONTH(r.date) = MONTH(:datecourant)')
            ->andWhere('DAY(r.date) = DAY(:datecourant)')
            ->andWhere('r.stationEnd is not null')
            ->andWhere('r.User = '.$user->getId())
            ->setParameter('datecourant', new \Datetime());
        return $qb
            ->getQuery()
            ->getResult()
            ;
    }

    public function findByUserAndThisWeek(User $user)
    {

        $qb =  $this->createQueryBuilder('r')
            ->where('YEAR(r.date) = YEAR(:datecourant)')
            ->andWhere('MONTH(r.date) = MONTH(:datecourant)')
            ->andWhere('WEEK(r.date) = WEEK(:datecourant)')
            ->andWhere('r.stationEnd is not null')
            ->andWhere('r.User = '.$user->getId())
            ->setParameter('datecourant', new \Datetime());
        return $qb
            ->getQuery()
            ->getResult()
            ;
    }

    public function findAllByUser(User $user)
    {

        $qb =  $this->createQueryBuilder('r')
            ->where('r.User = '.$user->getId());
        return $qb
            ->getQuery()
            ->getResult()
            ;
    }

    /*
    public function findOneBySomeField($value): ?Ride
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
