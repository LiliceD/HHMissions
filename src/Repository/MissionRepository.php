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

    public function findByStatus($value, $order)
    {
        $qb = $this->createQueryBuilder('m')
            ->innerJoin('m.accomodation', 'a')
            ->addSelect('a')
            ->innerJoin('m.gla', 'g')
            ->addSelect('g')
            ->leftJoin('m.volunteer', 'v')
            ->addSelect('v')
        ;
        
        return $qb->add('where', $qb->expr()->in('m.status', ':value'))
            ->orderBy('m.id', $order)
            ->setParameter('value', $value)
            // ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findAllJoined()
    {
        return $this->createQueryBuilder('m')
            ->innerJoin('m.accomodation', 'a')
            ->addSelect('a')
            ->innerJoin('m.gla', 'g')
            ->addSelect('g')
            ->leftJoin('m.volunteer', 'v')
            ->addSelect('v')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findByFiltersJoined($filters)
    {
        $qb = $this->createQueryBuilder('m')
            ->innerJoin('m.accomodation', 'a')
            ->addSelect('a')
            ->innerJoin('m.gla', 'g')
            ->addSelect('g')
            ->leftJoin('m.volunteer', 'v')
            ->addSelect('v')
        ;

        foreach($filters as $key => $filter) {
            $qb->andWhere($qb->expr()->in($filter['field'], '?'.$key))
                ->setParameter($key, $filter['value']);
        }

        return $qb->getQuery()
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
