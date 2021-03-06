<?php

namespace App\Manager;

use App\Entity\Address;
use App\Entity\BuildingInspection;
use App\Entity\BuildingInspectionItem;
use App\Repository\BuildingInspectionItemHeadersRepository;
use App\Repository\BuildingInspectionRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class BuildingInspectionManager
 *
 * @author Alice Dahan <lilice.dhn@gmail.com>
 */
class BuildingInspectionManager
{
    /** @var BuildingInspectionRepository */
    private $repository;

    /** @var BuildingInspectionItemHeadersRepository */
    private $headersRepository;

    /** @var EntityManagerInterface */
    private $entityManager;

    /**
     * BuildingInspectionManager constructor.
     *
     * @param BuildingInspectionRepository            $buildingInspectionRepository
     * @param BuildingInspectionItemHeadersRepository $headersRepository
     * @param EntityManagerInterface                  $entityManager
     */
    public function __construct(BuildingInspectionRepository $buildingInspectionRepository, BuildingInspectionItemHeadersRepository $headersRepository, EntityManagerInterface $entityManager)
    {
        $this->repository = $buildingInspectionRepository;
        $this->headersRepository = $headersRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @param int $id
     *
     * @return BuildingInspection|null
     */
    public function get(int $id): ?BuildingInspection
    {
        return $this->repository->findJoined($id);
    }

    /**
     * Get a new BuildingInspection with a set of new items associated to the list of headers
     *
     * @return BuildingInspection
     *
     * @throws \Exception
     */
    public function getNew(): BuildingInspection
    {
        $inspection = new BuildingInspection();
        $headers = $this->getItemHeaders();

        foreach ($headers as $header) {
            $item = new BuildingInspectionItem();
            $item->setHeaders($header);
            $inspection->addItem($item);
        }

        return $inspection;
    }

    /**
     * Get all BuildingInspectionItemHeaders
     *
     * @return array
     */
    public function getItemHeaders(): array
    {
        return $this->headersRepository->findBy([], ['rank' => 'asc']);
    }

    /**
     * Persist a BuildingInspection with its items
     *
     * @param BuildingInspection $inspection
     * @param bool               $persistItems
     * @param bool               $flush
     */
    public function persist(BuildingInspection $inspection, bool $persistItems = true, bool $flush = true): void
    {
        if ($persistItems) {
            $this->persistItems($inspection);
        }

        $this->entityManager->persist($inspection);

        if ($flush) {
            $this->entityManager->flush();
        }
    }

    /**
     * Get building inspections for a given address
     *
     * @param Address  $address
     * @param int|null $limit
     *
     * @return BuildingInspection[]
     */
    public function getByAddress(Address $address, int $limit = null): array
    {
        return $this->repository->findByAddressJoined($address->getId(), $limit);
    }

    /**
     * Persist a BuildingInspection's items
     *
     * @param BuildingInspection $inspection
     */
    private function persistItems(BuildingInspection $inspection): void
    {
        $items = $inspection->getItems()->getValues();

        foreach ($items as $item) {
            $this->entityManager->persist($item);
        }
    }
}
