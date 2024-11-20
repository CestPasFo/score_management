<?php

namespace App\Entity;

use App\Repository\EquipeRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: EquipeRepository::class)]
class Equipe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 30)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 30)]
    private ?string $nom = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Joueur $joueurs = null;

    #[ORM\Column(nullable: true)]
    private ?int $nbdefaite = null;

    #[ORM\Column(nullable: true)]
    private ?int $nbvictoire = null;

    #[ORM\Column]
    private ?int $nbmatch = null;

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

    public function getJoueurs(): ?Joueur
    {
        return $this->joueurs;
    }

    public function setJoueurs(?Joueur $joueurs): static
    {
        $this->joueurs = $joueurs;

        return $this;
    }

    public function getNbdefaite(): ?int
    {
        return $this->nbdefaite;
    }

    public function setNbdefaite(?int $nbdefaite): static
    {
        $this->nbdefaite = $nbdefaite;

        return $this;
    }

    public function getNbvictoire(): ?int
    {
        return $this->nbvictoire;
    }

    public function setNbvictoire(?int $nbvictoire): static
    {
        $this->nbvictoire = $nbvictoire;

        return $this;
    }

    public function getNbmatch(): ?int
    {
        return $this->nbmatch;
    }

    public function setNbmatch(int $nbmatch): static
    {
        $this->nbmatch = $nbmatch;

        return $this;
    }
}
