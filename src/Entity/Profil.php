<?php

namespace App\Entity;

use App\Repository\ProfilRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProfilRepository::class)]
class Profil
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $photoProfil = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $informationProfessionnelles = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $informationContact = null;

    #[ORM\OneToOne(inversedBy: 'profil', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateur $utilisateur = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPhotoProfil(): ?string
    {
        return $this->photoProfil;
    }

    public function setPhotoProfil(?string $photoProfil): static
    {
        $this->photoProfil = $photoProfil;

        return $this;
    }

    public function getInformationProfessionnelles(): ?string
    {
        return $this->informationProfessionnelles;
    }

    public function setInformationProfessionnelles(?string $informationProfessionnelles): static
    {
        $this->informationProfessionnelles = $informationProfessionnelles;

        return $this;
    }

    public function getInformationContact(): ?string
    {
        return $this->informationContact;
    }

    public function setInformationContact(?string $informationContact): static
    {
        $this->informationContact = $informationContact;

        return $this;
    }

    public function getUtilisateur(): ?Utilisateur
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(Utilisateur $utilisateur): static
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }
}
