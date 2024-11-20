<?php

namespace App\Entity;

use App\Repository\EquipeRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: EquipeRepository::class)]
class Equipe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["equipe:read"])]
    private ?int $id = null;

    #[ORM\Column(length: 30)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 30)]
    #[Groups(["equipe:read"])]
    private ?string $nom = null;

    #[ORM\Column(nullable: true)]
    private ?int $nbdefaite = null;

    #[ORM\Column(nullable: true)]
    private ?int $nbvictoire = null;

    #[ORM\OneToMany(targetEntity: Joueur::class, mappedBy: 'equipe')]
    #[Groups(["equipe:read"])]
    private Collection $joueurs;

    public function __construct()
    {
        $this->joueurs = new ArrayCollection();
    }

    #[ORM\Column]
    #[Groups(["equipe:read"])]
    private ?int $nbmatch = null;

    public function getJoueurs(): Collection
    {
        return $this->joueurs;
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
