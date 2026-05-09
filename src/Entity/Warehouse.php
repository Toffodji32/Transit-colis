<?php

namespace App\Entity;

use App\Repository\WarehouseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WarehouseRepository::class)]
#[ORM\Table(name: 'warehouse')]
class Warehouse
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $nom = null;

    #[ORM\Column(length: 100)]
    private ?string $pays = null;

    #[ORM\Column(length: 150)]
    private ?string $adresse = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $ville = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $telephone = null;

    #[ORM\Column(length: 180, nullable: true)]
    private ?string $email = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $horairesOuverture = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $capaciteMaximale = null;

    #[ORM\OneToMany(targetEntity: Colis::class, mappedBy: 'warehouseOrigine')]
    private Collection $colisOrigine;

    #[ORM\OneToMany(targetEntity: Colis::class, mappedBy: 'warehouseDestination')]
    private Collection $colisDestination;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $responsable = null;

    public function __construct()
    {
        $this->colisOrigine = new ArrayCollection();
        $this->colisDestination = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPays(): ?string
    {
        return $this->pays;
    }

    public function setPays(string $pays): static
    {
        $this->pays = $pays;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): static
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(?string $ville): static
    {
        $this->ville = $ville;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): static
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getHorairesOuverture(): ?string
    {
        return $this->horairesOuverture;
    }

    public function setHorairesOuverture(?string $horairesOuverture): static
    {
        $this->horairesOuverture = $horairesOuverture;

        return $this;
    }

    public function getCapaciteMaximale(): ?string
    {
        return $this->capaciteMaximale;
    }

    public function setCapaciteMaximale(?string $capaciteMaximale): static
    {
        $this->capaciteMaximale = $capaciteMaximale;

        return $this;
    }

    /**
     * @return Collection<int, Colis>
     */
    public function getColisOrigine(): Collection
    {
        return $this->colisOrigine;
    }

    public function addColisOrigine(Colis $colisOrigine): static
    {
        if (!$this->colisOrigine->contains($colisOrigine)) {
            $this->colisOrigine->add($colisOrigine);
            $colisOrigine->setWarehouseOrigine($this);
        }

        return $this;
    }

    public function removeColisOrigine(Colis $colisOrigine): static
    {
        if ($this->colisOrigine->removeElement($colisOrigine)) {
            // set the owning side to null (unless already changed)
            if ($colisOrigine->getWarehouseOrigine() === $this) {
                $colisOrigine->setWarehouseOrigine(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Colis>
     */
    public function getColisDestination(): Collection
    {
        return $this->colisDestination;
    }

    public function addColisDestination(Colis $colisDestination): static
    {
        if (!$this->colisDestination->contains($colisDestination)) {
            $this->colisDestination->add($colisDestination);
            $colisDestination->setWarehouseDestination($this);
        }

        return $this;
    }

    public function removeColisDestination(Colis $colisDestination): static
    {
        if ($this->colisDestination->removeElement($colisDestination)) {
            // set the owning side to null (unless already changed)
            if ($colisDestination->getWarehouseDestination() === $this) {
                $colisDestination->setWarehouseDestination(null);
            }
        }

        return $this;
    }

    public function getResponsable(): ?User
    {
        return $this->responsable;
    }

    public function setResponsable(?User $responsable): static
    {
        $this->responsable = $responsable;

        return $this;
    }
}

