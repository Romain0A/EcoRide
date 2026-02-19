<?php

namespace App\Entity;

use App\Repository\CovoiturageParticipantRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CovoiturageParticipantRepository::class)]
#[ORM\Table(name: "covoiturage_participant")]
class CovoiturageParticipant
{
#[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Covoiturage::class, inversedBy: "CovoiturageParticipant")]
    #[ORM\JoinColumn(name: "covoiturage_id", referencedColumnName: "id", onDelete: "CASCADE", nullable: false)]
    private ?Covoiturage $covoiturage = null;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Utilisateur::class, inversedBy: "CovoiturageParticipant")]
    #[ORM\JoinColumn(name: "utilisateur_id", referencedColumnName: "id", onDelete: "CASCADE", nullable: false)]
    private ?Utilisateur $passager = null;

    #[ORM\Column(type: "boolean")]
    private bool $confirme = false;

    public function getCovoiturage(): ?Covoiturage
    {
        return $this->covoiturage;
    }

    public function setCovoiturage(?Covoiturage $covoiturage): self
    {
        $this->covoiturage = $covoiturage;
        return $this;
    }

    public function getPassager(): ?Utilisateur
    {
        return $this->passager;
    }

    public function setPassager(?Utilisateur $passager): self
    {
        $this->passager = $passager;
        return $this;
    }

    public function isConfirme(): bool
    {
        return $this->confirme;
    }

    public function setConfirme(bool $confirme): self
    {
        $this->confirme = $confirme;
        return $this;
    }
}