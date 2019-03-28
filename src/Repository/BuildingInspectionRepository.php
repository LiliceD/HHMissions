<?php

namespace App\Repository;

use App\Entity\BuildingInspection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Class BuildingInspectionRepository
 *
 * @method BuildingInspection|null find($id, $lockMode = null, $lockVersion = null)
 * @method BuildingInspection|null findOneBy(array $criteria, array $orderBy = null)
 * @method BuildingInspection[]    findAll()
 * @method BuildingInspection[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 * @author Alice Dahan <lilice.dhn@gmail.com>
 */
class BuildingInspectionRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, BuildingInspection::class);
    }

    /**
     * @param int $id
     *
     * @return BuildingInspection|null
     */
    public function findJoined(int $id): ?BuildingInspection
    {
        try {
            return $this->createQueryBuilder('bi')
                ->leftJoin('bi.inspector', 'ins')
                ->addSelect('ins')
                ->innerJoin('bi.address', 'a')
                ->addSelect('a')
                ->innerJoin('a.gla', 'g')
                ->addSelect('g')
                ->leftJoin('a.referent', 'r')
                ->addSelect('r')
                ->innerJoin('bi.items', 'it')
                ->addSelect('it')
                ->where(sprintf('bi.id  = %d', $id))
                ->getQuery()
                ->getSingleResult();
        } catch (\Exception $ex) {
            return null;
        }
    }

    /**
     * @param int $addressId
     * @param int|null $limit
     *
     * @return BuildingInspection[]
     */
    public function findByAddressJoined(int $addressId, ?int $limit = null): array
    {
        return $this->createQueryBuilder('bi')
            ->leftJoin('bi.inspector', 'ins')
            ->addSelect('ins')
            ->innerJoin('bi.address', 'a')
            ->addSelect('a')
            ->innerJoin('a.gla', 'g')
            ->addSelect('g')
            ->leftJoin('a.referent', 'r')
            ->addSelect('r')
            ->innerJoin('bi.items', 'it')
            ->addSelect('it')
            ->where(sprintf('a.id  = %d', $addressId))
            ->orderBy('bi.created', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
