<?php

namespace App\Entity;

use App\Repository\HistoriqueStatutRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HistoriqueStatutRepository::class)]
#[ORM\Table(name: 'historique_statuts')]
class HistoriqueStatut
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'historiqueStatuts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Colis $colis = null;

    #[ORM\Column(length: 50)]
    private ?string $statut = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateChangement = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $commentaire = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $userModificateur = null;

    public function __construct()
    {
        $this->dateChangement = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getColis(): ?Colis
    {
        return $this->colis;
    }

    public function setColis(?Colis $colis): static
    {
        $this->colis = $colis;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): static
    {
        $this->statut = $statut;

        return $this;
    }

    public function getDateChangement(): ?\DateTimeInterface
    {
        return $this->dateChangement;
    }

    public function setDateChangement(\DateTimeInterface $dateChangement): static
    {
        $this->dateChangement = $dateChangement;

        return $this;
    }

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(?string $commentaire): static
    {
        $this->commentaire = $commentaire;

        return $this;
    }

    public function getUserModificateur(): ?User
    {
        return $this->userModificateur;
    }

    public function setUserModificateur(?User $userModificateur): static
    {
        $this->userModificateur = $userModificateur;

        return $this;
    }

    /**
     * Retourne le libellé du statut
     */
    public function getStatutLibelle(): ?string
    {
        return Colis::getStatuts()[$this->statut] ?? null;
    }
}

