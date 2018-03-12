<?php

namespace App\Repository;

use App\Entity\Mission;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class MissionRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Mission::class);
    }

    public function findByStatus($value)
    {
        $qb = $this->createQueryBuilder('m');
        return $qb->add('where', $qb->expr()->in('m.status', ':value'))
            ->setParameter('value', $value)
            ->orderBy('m.dateCreated', 'ASC')
            // ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }

    // public function findOneByIdJoinedToAccomodation($missionId)
    // {
    //     return $this->createQueryBuilder('m')
    //         ->innerJoin('m.accomodation', 'c')
    //         ->addSelect('c')
    //         ->andWhere('m.id = :id')
    //         ->setParameter('id', $misionId)
    //         ->getQuery()
    //         ->getOneOrNullResult();
    // }
}
