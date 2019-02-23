<?php

namespace App\Manager;

use App\Entity\Address;
use App\Entity\User;
use App\Repository\AddressRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class AddressManager
 *
 * @author Alice Dahan <lilice.dhn@gmail.com>
 */
class AddressManager
{
    /** @var AddressRepository */
    private $repository;

    /** @var EntityManagerInterface */
    private $entityManager;

    /**
     * AddressManager constructor.
     *
     * @param AddressRepository      $addressRepository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(AddressRepository $addressRepository, EntityManagerInterface $entityManager)
    {
        $this->repository = $addressRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * Persist an Address
     *
     * @param Address $address
     * @param bool $flush
     */
    public function persist(Address $address, bool $flush = true): void
    {
        $this->entityManager->persist($address);

        if ($flush) {
            $this->entityManager->flush();
        }
    }

    /**
     * Get all Addresses
     *
     * @return Address[]
     */
    public function getAll(): array
    {
        return $this->repository->findAllJoined();
    }

    /**
     * Get an Address from its ID
     *
     * @param int $id
     *
     * @return Address|null
     */
    public function get(int $id): ?Address
    {
        /** @var Address $address */
        $address = $this->repository->find($id);

        return $address;
    }

    /**
     * Get a given Address's access, owner type, gla and referent information
     *
     * @param Address $address
     *
     * @return array
     */
    public function getAddressInfo(Address $address): array
    {
        $glaUser = $address->getGla();
        $gla = [
            'id' => $glaUser->getId(),
            'name' => $glaUser->getName(),
        ];

        $referentUser = $address->getReferent();
        $referent = [
            'id' => 0,
            'name' => '',
        ];
        if ($referentUser instanceof User) {
            $referent = [
                'id' => $referentUser->getId(),
                'name' => $referentUser->getName(),
            ];
        }

        return [
            'access' => $address->getAccess(),
            'ownerType' => $address->getOwnerType(),
            'gla' => $gla,
            'referent' => $referent,
        ];
    }

    /**
     * @return array
     */
    public function getBuildings(): array
    {
        return $this->repository->getBuildings();
    }
}
