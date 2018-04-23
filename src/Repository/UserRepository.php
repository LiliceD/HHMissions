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

    public function qbActiveByCategory($category)
    {
        $qb = $this->createQueryBuilder('u');
        
        // Select active users
        $qb->select('u')
           ->where('active = true')
        ;
        
        // Possibly add filter on volunteer/gla
        switch ($category) {
            case 'gla':
                $qb->where('u.gla = true');
                break;
            case 'volunteer':
                $qb->where('u.volunteer = true');
        }

        // Order alphabetically by name
        // Return directly the query builder (for EntityType fields query_builder in MissionType)
        return $qb->orderBy('u.name', 'ASC');
    }

    public function qbAllByCategory($category)
    {
        $qb = $this->createQueryBuilder('u');
        
        // Select all users
        $qb->select('u');
        
        // Possibly add filter on volunteer/gla
        switch ($category) {
            case 'gla':
                $qb->where('u.gla = true');
                break;
            case 'volunteer':
                $qb->where('u.volunteer = true');
        }

        // Order alphabetically by name
        // Return directly the query builder (for EntityType fields query_builder in MissionSearchType)
        return $qb->orderBy('u.name', 'ASC');
    }
}
