<?php

namespace App\Repository;

use App\Entity\Mission;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Class MissionRepository
 *
 * @author Alice Dahan <lilice.dhn@gmail.com>
 */
class MissionRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Mission::class);
    }

    public function findByFiltersJoined(array $filters): array
    {
        $qb = $this->createQueryBuilder('m')
            ->innerJoin('m.address', 'a')
            ->addSelect('a')
            ->innerJoin('m.gla', 'g')
            ->addSelect('g')
            ->leftJoin('m.volunteer', 'v')
            ->addSelect('v')
        ;

        $parameters = [];
        foreach ($filters as $key => $filter) {
            if ($filter['field'] === 'm.description') {
            // For 'description' : search if it contains the value
                $qb->andWhere($qb->expr()->like($filter['field'], ':'.$key));
                $parameters[$key] = $filter['value'];
            } elseif (in_array($filter['field'], ['m.dateCreated', 'm.dateAssigned', 'm.dateFinished'])) {
            // For dates : search if it is greater than / lower than or between value(s)
                if (!$filter['value']['min']) {
                    $qb->andWhere($qb->expr()->lte($filter['field'], ':'.$key.'Max'));
                    $parameters[$key.'Max'] = $filter['value']['max'];
                } elseif (!$filter['value']['max']) {
                    $qb->andWhere($qb->expr()->gte($filter['field'], ':'.$key.'Min'));
                    $parameters[$key.'Min'] = $filter['value']['min'];
                } else {
                    $qb->andWhere($qb->expr()->between($filter['field'], ':'.$key.'Min', ':'.$key.'Max'));
                    $parameters[$key.'Min'] = $filter['value']['min'];
                    $parameters[$key.'Max'] = $filter['value']['max'];
                }
            } else {
            // For other fields : search if it is equal to value
                $qb->andWhere($qb->expr()->in($filter['field'], ':'.$key));
                $parameters[$key] = $filter['value'];
            }
        }
        $qb->setParameters($parameters)
            ->orderBy('m.id', 'DESC');

        // Sort and return missions
        return $qb->getQuery()
            ->getResult();
    }

    /**
     * Get a given User's non-closed Missions that have been updated by someone else since their last login
     *
     * @param User $user
     *
     * @return array
     */
    public function findUpdatedSinceLastLoginByUser(User $user): array
    {
        return $this->createQueryBuilder('m')
            ->innerJoin('m.gla', 'g')
            ->addSelect('g')
            ->leftJoin('m.volunteer', 'v')
            ->addSelect('v')
            ->leftJoin('m.contentLastUpdatedBy', 'up')
            ->addSelect('up')
            ->andWhere('m.status <> :statusClosed')
            ->andWhere('g.id = :userId OR v.id = :userId')
            ->andWhere('up.id <> :userId')
            ->andWhere('m.contentLastUpdatedAt > :userLastLogin')
            ->setParameters([
                'statusClosed' => Mission::STATUS_CLOSED,
                'userId' => $user->getId(),
                'userLastLogin' => $user->getLastLogin(),
            ])
            ->getQuery()
            ->getResult()
        ;
    }
}
