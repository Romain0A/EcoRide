<?php

namespace App\Entity;

use App\Entity\Avis;
use App\Entity\Utilisateur;
use App\Entity\Voiture;
use App\Repository\CovoiturageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CovoiturageRepository::class)]
class Covoiturage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $date_depart = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTime $heure_depart = null;

    #[ORM\Column(length: 50)]
    private ?string $lieu_depart = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $date_arrivee = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTime $heure_arrivee = null;

    #[ORM\Column(length: 50)]
    private ?string $lieu_arrivee = null;

    #[ORM\Column(length: 50)]
    private ?string $statut = null;

    #[ORM\Column]
    private ?int $nb_place = null;

    #[ORM\Column]
    private ?float $prix_personne = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $debut_trajet = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $fin_trajet = null;

    #[ORM\OneToMany(mappedBy: "covoiturage", targetEntity: CovoiturageParticipant::class, cascade: ["persist", "remove"])]
    private Collection $CovoiturageParticipant;

    #[ORM\ManyToOne(inversedBy: 'Covoiturages_chauffeur')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateur $chauffeur = null;

    /**
     * @var Collection<int, Avis>
     */
    #[ORM\OneToMany(targetEntity: Avis::class, mappedBy: 'covoiturage')]
    private Collection $avis;

    #[ORM\ManyToOne(inversedBy: 'covoiturages')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Voiture $voiture = null;

    public function __construct()
    {
        $this->CovoiturageParticipant = new ArrayCollection();
        $this->avis = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateDepart(): ?\DateTime
    {
        return $this->date_depart;
    }

    public function setDateDepart(\DateTime $date_depart): static
    {
        $this->date_depart = $date_depart;

        return $this;
    }

    public function getHeureDepart(): ?\DateTime
    {
        return $this->heure_depart;
    }

    public function setHeureDepart(\DateTime $heure_depart): static
    {
        $this->heure_depart = $heure_depart;

        return $this;
    }

    public function getLieuDepart(): ?string
    {
        return $this->lieu_depart;
    }

    public function setLieuDepart(string $lieu_depart): static
    {
        $this->lieu_depart = $lieu_depart;

        return $this;
    }

    public function getDateArrivee(): ?\DateTime
    {
        return $this->date_arrivee;
    }

    public function setDateArrivee(\DateTime $date_arrivee): static
    {
        $this->date_arrivee = $date_arrivee;

        return $this;
    }

    public function getHeureArrivee(): ?\DateTime
    {
        return $this->heure_arrivee;
    }

    public function setHeureArrivee(\DateTime $heure_arrivee): static
    {
        $this->heure_arrivee = $heure_arrivee;

        return $this;
    }

    public function getLieuArrivee(): ?string
    {
        return $this->lieu_arrivee;
    }

    public function setLieuArrivee(string $lieu_arrivee): static
    {
        $this->lieu_arrivee = $lieu_arrivee;

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

    public function getNbPlace(): ?int
    {
        return $this->nb_place;
    }

    public function setNbPlace(int $nb_place): static
    {
        $this->nb_place = $nb_place;

        return $this;
    }

    public function getPrixPersonne(): ?float
    {
        return $this->prix_personne;
    }

    public function setPrixPersonne(float $prix_personne): static
    {
        $this->prix_personne = $prix_personne;

        return $this;
    }

    public function getDebutTrajet(): ?\DateTime
    {
        return $this->debut_trajet;
    }

    public function setDebutTrajet(?\DateTime $debut_trajet): static
    {
        $this->debut_trajet = $debut_trajet;

        return $this;
    }

    public function getFinTrajet(): ?\DateTime
    {
        return $this->fin_trajet;
    }

    public function setFinTrajet(?\DateTime $fin_trajet): static
    {
        $this->fin_trajet = $fin_trajet;

        return $this;
    }

    public function getCovoiturageParticipant(): Collection
    {
        return $this->CovoiturageParticipant;
    }

    public function getChauffeur(): ?Utilisateur
    {
        return $this->chauffeur;
    }

    public function setChauffeur(?Utilisateur $chauffeur): static
    {
        $this->chauffeur = $chauffeur;

        return $this;
    }

    /**
     * @return Collection<int, Avis>
     */
    public function getAvis(): Collection
    {
        return $this->avis;
    }

    public function addAvi(Avis $avi): static
    {
        if (!$this->avis->contains($avi)) {
            $this->avis->add($avi);
            $avi->setCovoiturage($this);
        }

        return $this;
    }

    public function removeAvi(Avis $avi): static
    {
        if ($this->avis->removeElement($avi)) {
            // set the owning side to null (unless already changed)
            if ($avi->getCovoiturage() === $this) {
                $avi->setCovoiturage(null);
            }
        }

        return $this;
    }

    public function hasAvisFromUser(Utilisateur $user): bool
    {
        foreach ($this->avis as $avis) {
            if ($avis->getPoster() === $user) {
                return true;
            }
        }
        return false;
    }

    public function getVoiture(): ?Voiture
    {
        return $this->voiture;
    }

    public function setVoiture(?Voiture $voiture): static
    {
        $this->voiture = $voiture;

        return $this;
    }
}
