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
}
