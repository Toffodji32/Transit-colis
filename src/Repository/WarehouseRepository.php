<?php

namespace App\Repository;

use App\Entity\Warehouse;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Warehouse>
 */
class WarehouseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Warehouse::class);
    }

    /**
     * Trouve un entrepôt par son nom
     */
    public function findByNom(string $nom): ?Warehouse
    {
        return $this->createQueryBuilder('w')
            ->where('w.nom = :nom')
            ->setParameter('nom', $nom)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Trouve l'entrepôt géré par un responsable
     */
    public function findByResponsable(int $responsableId): ?Warehouse
    {
        return $this->createQueryBuilder('w')
            ->where('w.responsable = :responsableId')
            ->setParameter('responsableId', $responsableId)
            ->getQuery()
            ->getOneOrNullResult();
    }
}

