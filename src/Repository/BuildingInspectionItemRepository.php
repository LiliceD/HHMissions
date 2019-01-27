<?php

namespace App\Repository;

use App\Entity\BuildingInspectionItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Class BuildingInspectionItemRepository
 *
 * @method BuildingInspectionItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method BuildingInspectionItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method BuildingInspectionItem[]    findAll()
 * @method BuildingInspectionItem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 * @author Alice Dahan <lilice.dhn@gmail.com>
 */
class BuildingInspectionItemRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, BuildingInspectionItem::class);
    }
}
