<?php

namespace App\Repository;

use App\Entity\Tarif;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Tarif>
 */
class TarifRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tarif::class);
    }

    /**
     * Trouve le tarif actif pour une route et un poids donné
     */
    public function findTarifActif(string $route, string $poids): ?Tarif
    {
        $tarifs = $this->findBy([
            'route' => $route,
            'actif' => true
        ]);

        foreach ($tarifs as $tarif) {
            if ($tarif->isApplicableForPoids($poids)) {
                return $tarif;
            }
        }

        return null;
    }

    /**
     * Trouve tous les tarifs actifs
     */
    public function findTarifsActifs(): array
    {
        return $this->findBy(['actif' => true]);
    }
}

