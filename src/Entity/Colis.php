<?php

namespace App\Entity;

use App\Repository\ColisRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ColisRepository::class)]
#[ORM\Table(name: 'colis')]
#[ORM\UniqueConstraint(name: 'UNIQ_NUMERO_COLIS', fields: ['numeroColis'])]
class Colis
{
    // Statuts disponibles
    public const STATUT_CREE = 'cree';
    public const STATUT_ENREGISTRE = 'enregistre';
    public const STATUT_EXPEDITION_EN_COURS = 'expedition_en_cours';
    public const STATUT_EN_PREPARATION = 'en_preparation';
    public const STATUT_EN_TRANSIT = 'en_transit';
    public const STATUT_ARRIVE_DESTINATION = 'arrive_destination';
    public const STATUT_PRET_RETRAIT = 'pret_retrait';
    public const STATUT_LIVRE = 'livre';
    public const STATUT_PROBLEME = 'probleme';
    public const STATUT_DOMMAGE = 'dommage';

    // Pays disponibles
    public const PAYS_NIGERIA = 'Nigeria';
    public const PAYS_BENIN = 'Benin';

    // Statut de paiement
    public const PAIEMENT_NON_PAYE = 'non_paye';
    public const PAIEMENT_PAYE = 'paye';
    public const PAIEMENT_PARTIEL = 'partiel';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50, unique: true)]
    private ?string $numeroColis = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $poids = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 50)]
    private ?string $paysOrigine = null;

    #[ORM\Column(length: 50)]
    private ?string $paysDestination = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $images = null;

    // Informations expéditeur
    #[ORM\Column(length: 100)]
    private ?string $expediteurNom = null;

    #[ORM\Column(length: 20)]
    private ?string $expediteurTel = null;

    #[ORM\Column(length: 180, nullable: true)]
    private ?string $expediteurEmail = null;

    // Informations destinataire
    #[ORM\Column(length: 100)]
    private ?string $destinataireNom = null;

    #[ORM\Column(length: 20)]
    private ?string $destinataireTel = null;

    #[ORM\Column(length: 180)]
    private ?string $destinataireEmail = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $destinataireAdresse = null;

    // Informations de suivi
    #[ORM\Column(length: 50)]
    private ?string $statutActuel = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateEnregistrement = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateDepart = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateLivraison = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateLivraisonEstimee = null;

    // Informations financières
    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $montantFrais = null;

    #[ORM\Column(length: 20)]
    private ?string $statutPaiement = null;

    // Relations
    #[ORM\ManyToOne(inversedBy: 'colis')]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'colisOrigine')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Warehouse $warehouseOrigine = null;

    #[ORM\ManyToOne(inversedBy: 'colisDestination')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Warehouse $warehouseDestination = null;

    #[ORM\OneToMany(targetEntity: HistoriqueStatut::class, mappedBy: 'colis', orphanRemoval: true, cascade: ['persist'])]
    private Collection $historiqueStatuts;

    public function __construct()
    {
        $this->historiqueStatuts = new ArrayCollection();
        $this->dateEnregistrement = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumeroColis(): ?string
    {
        return $this->numeroColis;
    }

    public function setNumeroColis(string $numeroColis): static
    {
        $this->numeroColis = $numeroColis;

        return $this;
    }

    public function getPoids(): ?string
    {
        return $this->poids;
    }

    public function setPoids(string $poids): static
    {
        $this->poids = $poids;

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

    public function getPaysOrigine(): ?string
    {
        return $this->paysOrigine;
    }

    public function setPaysOrigine(string $paysOrigine): static
    {
        $this->paysOrigine = $paysOrigine;

        return $this;
    }

    public function getPaysDestination(): ?string
    {
        return $this->paysDestination;
    }

    public function setPaysDestination(string $paysDestination): static
    {
        $this->paysDestination = $paysDestination;

        return $this;
    }

    public function getImages(): ?array
    {
        return $this->images;
    }

    public function setImages(?array $images): static
    {
        $this->images = $images;

        return $this;
    }

    public function getExpediteurNom(): ?string
    {
        return $this->expediteurNom;
    }

    public function setExpediteurNom(string $expediteurNom): static
    {
        $this->expediteurNom = $expediteurNom;

        return $this;
    }

    public function getExpediteurTel(): ?string
    {
        return $this->expediteurTel;
    }

    public function setExpediteurTel(string $expediteurTel): static
    {
        $this->expediteurTel = $expediteurTel;

        return $this;
    }

    public function getExpediteurEmail(): ?string
    {
        return $this->expediteurEmail;
    }

    public function setExpediteurEmail(?string $expediteurEmail): static
    {
        $this->expediteurEmail = $expediteurEmail;

        return $this;
    }

    public function getDestinataireNom(): ?string
    {
        return $this->destinataireNom;
    }

    public function setDestinataireNom(string $destinataireNom): static
    {
        $this->destinataireNom = $destinataireNom;

        return $this;
    }

    public function getDestinataireTel(): ?string
    {
        return $this->destinataireTel;
    }

    public function setDestinataireTel(string $destinataireTel): static
    {
        $this->destinataireTel = $destinataireTel;

        return $this;
    }

    public function getDestinataireEmail(): ?string
    {
        return $this->destinataireEmail;
    }

    public function setDestinataireEmail(string $destinataireEmail): static
    {
        $this->destinataireEmail = $destinataireEmail;

        return $this;
    }

    public function getDestinataireAdresse(): ?string
    {
        return $this->destinataireAdresse;
    }

    public function setDestinataireAdresse(?string $destinataireAdresse): static
    {
        $this->destinataireAdresse = $destinataireAdresse;

        return $this;
    }

    public function getStatutActuel(): ?string
    {
        return $this->statutActuel;
    }

    public function setStatutActuel(string $statutActuel): static
    {
        $this->statutActuel = $statutActuel;

        return $this;
    }

    public function getDateEnregistrement(): ?\DateTimeInterface
    {
        return $this->dateEnregistrement;
    }

    public function setDateEnregistrement(\DateTimeInterface $dateEnregistrement): static
    {
        $this->dateEnregistrement = $dateEnregistrement;

        return $this;
    }

    public function getDateDepart(): ?\DateTimeInterface
    {
        return $this->dateDepart;
    }

    public function setDateDepart(?\DateTimeInterface $dateDepart): static
    {
        $this->dateDepart = $dateDepart;

        return $this;
    }

    public function getDateLivraison(): ?\DateTimeInterface
    {
        return $this->dateLivraison;
    }

    public function setDateLivraison(?\DateTimeInterface $dateLivraison): static
    {
        $this->dateLivraison = $dateLivraison;

        return $this;
    }

    public function getDateLivraisonEstimee(): ?\DateTimeInterface
    {
        return $this->dateLivraisonEstimee;
    }

    public function setDateLivraisonEstimee(?\DateTimeInterface $dateLivraisonEstimee): static
    {
        $this->dateLivraisonEstimee = $dateLivraisonEstimee;

        return $this;
    }

    public function getMontantFrais(): ?string
    {
        return $this->montantFrais;
    }

    public function setMontantFrais(string $montantFrais): static
    {
        $this->montantFrais = $montantFrais;

        return $this;
    }

    public function getStatutPaiement(): ?string
    {
        return $this->statutPaiement;
    }

    public function setStatutPaiement(string $statutPaiement): static
    {
        $this->statutPaiement = $statutPaiement;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getWarehouseOrigine(): ?Warehouse
    {
        return $this->warehouseOrigine;
    }

    public function setWarehouseOrigine(?Warehouse $warehouseOrigine): static
    {
        $this->warehouseOrigine = $warehouseOrigine;

        return $this;
    }

    public function getWarehouseDestination(): ?Warehouse
    {
        return $this->warehouseDestination;
    }

    public function setWarehouseDestination(?Warehouse $warehouseDestination): static
    {
        $this->warehouseDestination = $warehouseDestination;

        return $this;
    }

    /**
     * @return Collection<int, HistoriqueStatut>
     */
    public function getHistoriqueStatuts(): Collection
    {
        return $this->historiqueStatuts;
    }

    public function addHistoriqueStatut(HistoriqueStatut $historiqueStatut): static
    {
        if (!$this->historiqueStatuts->contains($historiqueStatut)) {
            $this->historiqueStatuts->add($historiqueStatut);
            $historiqueStatut->setColis($this);
        }

        return $this;
    }

    public function removeHistoriqueStatut(HistoriqueStatut $historiqueStatut): static
    {
        if ($this->historiqueStatuts->removeElement($historiqueStatut)) {
            // set the owning side to null (unless already changed)
            if ($historiqueStatut->getColis() === $this) {
                $historiqueStatut->setColis(null);
            }
        }

        return $this;
    }

    /**
     * Retourne un tableau de tous les statuts disponibles
     */
    public static function getStatuts(): array
    {
        return [
            self::STATUT_CREE => 'Créé',
            self::STATUT_ENREGISTRE => 'Enregistré',
            self::STATUT_EXPEDITION_EN_COURS => 'Expédition en cours',
            self::STATUT_EN_PREPARATION => 'En préparation',
            self::STATUT_EN_TRANSIT => 'En transit',
            self::STATUT_ARRIVE_DESTINATION => 'Arrivé à destination',
            self::STATUT_PRET_RETRAIT => 'Prêt pour retrait',
            self::STATUT_LIVRE => 'Livré',
            self::STATUT_PROBLEME => 'Problème',
            self::STATUT_DOMMAGE => 'Dommage',
        ];
    }

    /**
     * Retourne le libellé du statut actuel
     */
    public function getStatutActuelLibelle(): ?string
    {
        return self::getStatuts()[$this->statutActuel] ?? null;
    }
}

