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
    public function getBuildings(): array
    {
        return $this->qbBuildings()
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
}
