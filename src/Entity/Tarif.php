<?php

namespace App\Entity;

use App\Repository\TarifRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TarifRepository::class)]
#[ORM\Table(name: 'tarifs')]
class Tarif
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $route = null; // Nigeria→Bénin / Bénin→Nigeria

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $poidsMin = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $poidsMax = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $prixParKg = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateDebut = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateFin = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: 'boolean')]
    private bool $actif = true;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRoute(): ?string
    {
        return $this->route;
    }

    public function setRoute(string $route): static
    {
        $this->route = $route;

        return $this;
    }

    public function getPoidsMin(): ?string
    {
        return $this->poidsMin;
    }

    public function setPoidsMin(?string $poidsMin): static
    {
        $this->poidsMin = $poidsMin;

        return $this;
    }

    public function getPoidsMax(): ?string
    {
        return $this->poidsMax;
    }

    public function setPoidsMax(?string $poidsMax): static
    {
        $this->poidsMax = $poidsMax;

        return $this;
    }

    public function getPrixParKg(): ?string
    {
        return $this->prixParKg;
    }

    public function setPrixParKg(string $prixParKg): static
    {
        $this->prixParKg = $prixParKg;

        return $this;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->dateDebut;
    }

    public function setDateDebut(\DateTimeInterface $dateDebut): static
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->dateFin;
    }

    public function setDateFin(?\DateTimeInterface $dateFin): static
    {
        $this->dateFin = $dateFin;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function isActif(): bool
    {
        return $this->actif;
    }

    public function setActif(bool $actif): static
    {
        $this->actif = $actif;

        return $this;
    }

    /**
     * Vérifie si ce tarif est actif pour un poids donné
     */
    public function isApplicableForPoids(string $poids): bool
    {
        $poidsFloat = (float) $poids;
        
        if (!$this->actif) {
            return false;
        }

        $now = new \DateTime();
        if ($this->dateDebut > $now) {
            return false;
        }

        if ($this->dateFin && $this->dateFin < $now) {
            return false;
        }

        if ($this->poidsMin !== null && $poidsFloat < (float) $this->poidsMin) {
            return false;
        }

        if ($this->poidsMax !== null && $poidsFloat > (float) $this->poidsMax) {
            return false;
        }

        return true;
    }

    /**
     * Calcule le prix pour un poids donné
     */
    public function calculerPrix(string $poids): string
    {
        if (!$this->isApplicableForPoids($poids)) {
            throw new \LogicException('Tarif non applicable pour ce poids');
        }

        $prix = (float) $poids * (float) $this->prixParKg;
        
        return number_format($prix, 2, '.', '');
    }
}

