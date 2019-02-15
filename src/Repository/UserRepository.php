<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Class UserRepository
 *
 * @author Alice Dahan <lilice.dhn@gmail.com>
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function qbActiveByCategory($category): QueryBuilder
    {
        $qb = $this->createQueryBuilder('u');
        
        // Select active users
        $qb->select('u')
           ->where('u.active = true')
        ;
        
        // Possibly add filter on volunteer/gla
        switch ($category) {
            case User::CATEGORY_GLA:
                $qb->andWhere('u.gla = true');
                break;
            case User::CATEGORY_VOLUNTEER:
                $qb->andWhere('u.volunteer = true');
        }

        // Return directly the query builder (for EntityType fields query_builder in MissionType)
        return $qb->orderBy('u.name', 'ASC');
    }

    public function qbAllByCategory($category): QueryBuilder
    {
        $qb = $this->createQueryBuilder('u');
        
        // Select all users
        $qb->select('u');
        
        // Possibly add filter on volunteer/gla
        switch ($category) {
            case User::CATEGORY_GLA:
                $qb->where('u.gla = true');
                break;
            case User::CATEGORY_VOLUNTEER:
                $qb->where('u.volunteer = true');
        }

        // Order alphabetically by name
        // Return directly the query builder (for EntityType fields query_builder in MissionSearchType)
        return $qb->orderBy('u.name', 'ASC');
    }
}
