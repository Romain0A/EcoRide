<?php

namespace App\Entity;

use App\Entity\Avis;
use App\Entity\Covoiturage;
use App\Entity\Voiture;
use App\Repository\UtilisateurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\OneToMany;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UtilisateurRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_PSEUDO', fields: ['pseudo'])]
#[UniqueEntity(fields: ['email'], message: 'Cet email est déjà utilisé.')]
#[UniqueEntity(fields: ['pseudo'], message: 'Ce pseudo est déjà utilisé.')]
class Utilisateur implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $pseudo = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 50)]
    private ?string $email = null;

    #[ORM\Column(length: 50)]
    private ?string $nom = null;

    #[ORM\Column(length: 50)]
    private ?string $prenom = null;

    #[ORM\Column(length: 15)]
    private ?string $telephone = null;

    #[ORM\Column(length: 50)]
    private ?string $adresse = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $date_naissance = null;

    #[ORM\Column(length: 255)]
    private ?string $photo = "img\icones\material-symbols--account-box.png";

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $note = 3;

    /**
     * @var Collection<int, Voiture>
     */
    #[ORM\OneToMany(targetEntity: Voiture::class, mappedBy: 'utilisateur', orphanRemoval: true)]
    private Collection $voitures;

    #[ORM\OneToMany(mappedBy: "passager", targetEntity: CovoiturageParticipant::class, cascade: ["persist", "remove"])]
    private Collection $CovoiturageParticipant;

    /**
     * @var Collection<int, Covoiturage>
     */
    #[ORM\OneToMany(targetEntity: Covoiturage::class, mappedBy: 'chauffeur')]
    private Collection $Covoiturages_chauffeur;

    /**
     * @var Collection<int, Avis>
     */
    #[ORM\OneToMany(targetEntity: Avis::class, mappedBy: 'poster')]
    private Collection $avis_poster;

    /**
     * @var Collection<int, Avis>
     */
    #[ORM\OneToMany(targetEntity: Avis::class, mappedBy: 'receveur')]
    private Collection $avis_recus;

    #[ORM\Column]
    private ?int $credit = 20;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $preference = null;

    public function __construct()
    {
        $this->voitures = new ArrayCollection();
        $this->CovoiturageParticipant = new ArrayCollection();
        $this->Covoiturages_chauffeur = new ArrayCollection();
        $this->avis_poster = new ArrayCollection();
        $this->avis_recus = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): static
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->pseudo;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Ensure the session doesn't contain actual password hashes by CRC32C-hashing them, as supported since Symfony 7.3.
     */
    public function __serialize(): array
    {
        $data = (array) $this;
        $data["\0".self::class."\0password"] = hash('crc32c', $this->password);

        return $data;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
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

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): static
    {
        $this->telephone = $telephone;

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

    public function getDateNaissance(): ?\DateTime
    {
        return $this->date_naissance;
    }

    public function setDateNaissance(\DateTime $date_naissance): static
    {
        $this->date_naissance = $date_naissance;

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(string $photo): static
    {
        $this->photo = $photo;

        return $this;
    }

    public function getNote(): ?int
    {
        return $this->note;
    }

    public function setNote(int $note): static
    {
        $this->note = $note;

        return $this;
    }

    /**
     * @return Collection<int, Voiture>
     */
    public function getVoitures(): Collection
    {
        return $this->voitures;
    }

    public function addVoiture(Voiture $voiture): static
    {
        if (!$this->voitures->contains($voiture)) {
            $this->voitures->add($voiture);
            $voiture->setUtilisateur($this);
        }

        return $this;
    }

    public function removeVoiture(Voiture $voiture): static
    {
        if ($this->voitures->removeElement($voiture)) {
            // set the owning side to null (unless already changed)
            if ($voiture->getUtilisateur() === $this) {
                $voiture->setUtilisateur(null);
            }
        }

        return $this;
    }

    public function getCovoiturageParticipant(): Collection
    {
        return $this->CovoiturageParticipant;
    }

    /**
     * @return Collection<int, Covoiturage>
     */
    public function getCovoituragesChauffeur(): Collection
    {
        return $this->Covoiturages_chauffeur;
    }

    public function addCovoituragesChauffeur(Covoiturage $covoituragesChauffeur): static
    {
        if (!$this->Covoiturages_chauffeur->contains($covoituragesChauffeur)) {
            $this->Covoiturages_chauffeur->add($covoituragesChauffeur);
            $covoituragesChauffeur->setChauffeur($this);
        }

        return $this;
    }

    public function removeCovoituragesChauffeur(Covoiturage $covoituragesChauffeur): static
    {
        if ($this->Covoiturages_chauffeur->removeElement($covoituragesChauffeur)) {
            // set the owning side to null (unless already changed)
            if ($covoituragesChauffeur->getChauffeur() === $this) {
                $covoituragesChauffeur->setChauffeur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Avis>
     */
    public function getAvisPoster(): Collection
    {
        return $this->avis_poster;
    }

    public function addAvisPoster(Avis $avisPoster): static
    {
        if (!$this->avis_poster->contains($avisPoster)) {
            $this->avis_poster->add($avisPoster);
            $avisPoster->setPoster($this);
        }

        return $this;
    }

    public function removeAvisPoster(Avis $avisPoster): static
    {
        if ($this->avis_poster->removeElement($avisPoster)) {
            // set the owning side to null (unless already changed)
            if ($avisPoster->getPoster() === $this) {
                $avisPoster->setPoster(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Avis>
     */
    public function getAvisRecus(): Collection
    {
        return $this->avis_recus;
    }

    public function addAvisRecu(Avis $avisRecu): static
    {
        if (!$this->avis_recus->contains($avisRecu)) {
            $this->avis_recus->add($avisRecu);
            $avisRecu->setReceveur($this);
        }

        return $this;
    }

    public function removeAvisRecu(Avis $avisRecu): static
    {
        if ($this->avis_recus->removeElement($avisRecu)) {
            // set the owning side to null (unless already changed)
            if ($avisRecu->getReceveur() === $this) {
                $avisRecu->setReceveur(null);
            }
        }

        return $this;
    }

    public function getCredit(): ?int
    {
        return $this->credit;
    }

    public function setCredit(int $credit): static
    {
        $this->credit = $credit;

        return $this;
    }

    public function getPreference(): ?string
    {
        return $this->preference;
    }

    public function setPreference(?string $preference): static
    {
        $this->preference = $preference;

        return $this;
    }
}
