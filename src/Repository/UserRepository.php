<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class UserRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findNameByCategory($category)
    {
        $qb = $this->createQueryBuilder('u');
        $qb->select('u.name');
        
        if ($category === 'volunteer') {
            $qb->add('where', $qb->expr()->in('u.isVolunteer', 'true'));
        } elseif ($category === 'gla') {
            $qb->add('where', $qb->expr()->in('u.isGla', 'true'));
        }
        
        return $qb->orderBy('u.name', 'ASC')
            ->getQuery()
            ->getArrayResult()
        ;
    }
}
