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

    public function findByStatusJoined($value)
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
            ->orderBy('m.id', 'DESC')
            ->setParameter('value', $value)
            // ->setMaxResults(10)
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
            if ($filter['field'] === 'm.description') {
            // For 'description' : search if it contains the value
                $qb->andWhere($qb->expr()->like($filter['field'], '?'.$key))
                    ->setParameter($key, $filter['value']);
            } else if (in_array($filter['field'], ['m.dateCreated', 'm.dateFinished'])) {
            // For dates : search if it is greater than / lower than or between value(s)
                if (!$filter['value']['min']) {
                    $qb->andWhere($qb->expr()->lte($filter['field'], ':max'))
                        ->setParameter('max', $filter['value']['max']);
                } else if (!$filter['value']['max']) {
                    $qb->andWhere($qb->expr()->gte($filter['field'], ':min'))
                        ->setParameter('min', $filter['value']['min']);
                } else {
                    $qb->andWhere($qb->expr()->between($filter['field'], ':min', ':max'))
                        ->setParameters(['min' => $filter['value']['min'], 'max' => $filter['value']['max']]);
                }
            } else {
            // For other fields : search if it is equal to value
                $qb->andWhere($qb->expr()->in($filter['field'], '?'.$key))
                    ->setParameter($key, $filter['value']);
            }
        }

        // Sort and return missions
        return $qb->orderBy('m.id', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }
}
