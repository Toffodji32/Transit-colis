<?php

namespace App\Repository;

use App\Entity\Colis;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Colis>
 */
class ColisRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Colis::class);
    }

    /**
     * Trouve un colis par son numéro
     */
    public function findByNumero(string $numero): ?Colis
    {
        return $this->createQueryBuilder('c')
            ->where('c.numeroColis = :numero')
            ->setParameter('numero', $numero)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Trouve tous les colis d'un utilisateur
     */
    public function findByUser(int $userId): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.user = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('c.dateEnregistrement', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Compte les colis par statut
     */
    public function countByStatut(string $statut): int
    {
        return $this->count(['statutActuel' => $statut]);
    }
}

