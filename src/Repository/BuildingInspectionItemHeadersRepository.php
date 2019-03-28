<?php

namespace App\Repository;

use App\Entity\BuildingInspectionItemHeaders;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Class BuildingInspectionItemHeadersRepository
 *
 * @method BuildingInspectionItemHeaders|null find($id, $lockMode = null, $lockVersion = null)
 * @method BuildingInspectionItemHeaders|null findOneBy(array $criteria, array $orderBy = null)
 * @method BuildingInspectionItemHeaders[]    findAll()
 * @method BuildingInspectionItemHeaders[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 * @author Alice Dahan <lilice.dhn@gmail.com>
 */
class BuildingInspectionItemHeadersRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, BuildingInspectionItemHeaders::class);
    }
}
