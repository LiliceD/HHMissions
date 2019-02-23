<?php

namespace App\Repository;

use App\Entity\Address;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Class AddressRepository
 *
 * @author Alice Dahan <lilice.dhn@gmail.com>
 */
class AddressRepository extends ServiceEntityRepository
{
    /**
     * AddressRepository constructor.
     *
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Address::class);
    }

    /**
     * @return array
     */
    public function getBuildingsJoined(): array
    {
        return $this->qbBuildings()
            ->innerJoin('a.gla', 'g')
            ->addSelect('g')
            ->leftJoin('a.referent', 'r')
            ->addSelect('r')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return QueryBuilder
     */
    public function qbBuildings(): QueryBuilder
    {
        return $this->createQueryBuilder('a')
            ->where(sprintf("a.ownerType = '%s'", Address::OWNER_FFH_BUILDING))
            ->orWhere(sprintf("a.ownerType = '%s'", Address::OWNER_PRIVATE_BUILDING))
            ->orderBy('a.street', 'ASC')
        ;
    }

    /**
     * @return array
     */
    public function findAllJoined(): array
    {
        return $this->createQueryBuilder('a')
            ->innerJoin('a.gla', 'g')
            ->addSelect('g')
            ->leftJoin('a.referent', 'r')
            ->addSelect('r')
            ->orderBy('a.street', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
