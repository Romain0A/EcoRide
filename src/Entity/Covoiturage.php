<?php

class Covoiturage{
    private int $id;
    private string $pfp;
    private string $chauffeur;
    private int $place;
    private int $prix;
    private string $depart;
    private string $arrivee;
    private bool $ecologique;
    private int $note;

    public function __construct(
        int $id,
        string $chauffeur,
        int $place,
        int $prix,
        string $depart,
        string $arrivee,
        bool $ecologique,
        string $pfp,
        int $note
        
    ) {
        $this->id = $id;
        $this->chauffeur = $chauffeur;
        $this->place = $place;
        $this->prix = $prix;
        $this->depart = $depart;
        $this->arrivee = $arrivee;
        $this->ecologique = $ecologique;
        $this->pfp = $pfp;
        $this->note = $note;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getChauffeur(): string
    {
        return $this->chauffeur;
    }

    public function getPlace(): int
    {
        return $this->place;
    }

    public function getPrix(): int
    {
        return $this->prix;
    }

    public function getDepart(): string
    {
        return $this->depart;
    }

    public function getArrivee(): string
    {
        return $this->arrivee;
    }

    public function isEcologique(): bool
    {
        return $this->ecologique;
    }

    public function getPfp(): string
    {
        return $this->pfp;
    }

    public function getNote(): int
    {
        return $this->note;
    }
}