<?php

namespace App\Repository;

use App\Entity\User;
use App\Utils\Constant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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

    public function qbActiveByCategory($category)
    {
        $qb = $this->createQueryBuilder('u');
        
        // Select active users
        $qb->select('u')
           ->where('active = true')
        ;
        
        // Possibly add filter on volunteer/gla
        switch ($category) {
            case Constant::CAT_GLA:
                $qb->where('u.gla = true');
                break;
            case Constant::CAT_VOLUNTEER:
                $qb->where('u.volunteer = true');
        }

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
            case Constant::CAT_GLA:
                $qb->where('u.gla = true');
                break;
            case Constant::CAT_VOLUNTEER:
                $qb->where('u.volunteer = true');
        }

        // Order alphabetically by name
        // Return directly the query builder (for EntityType fields query_builder in MissionSearchType)
        return $qb->orderBy('u.name', 'ASC');
    }
}
