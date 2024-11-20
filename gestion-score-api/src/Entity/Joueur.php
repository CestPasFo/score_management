<?php

namespace App\Entity;

use App\Repository\JoueurRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: JoueurRepository::class)]
class Joueur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["joueur:read"])]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 20)]
    #[Groups(["joueur:read"])]
    private int $equipeId = null;

    #[ORM\Column(length: 20)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 20)]
    #[Groups(["joueur:read"])]
    private ?string $firstname = null;

    #[ORM\Column(length: 20)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 20)]
    #[Groups(["joueur:read"])]
    private ?string $name = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEquipeID(): ?int
    {
        return $this->equipeId;
    }

    public function setEquipeID(int $equipeId)
    {
        $this->equipeId = $equipeId;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }



    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    #[ORM\ManyToOne(targetEntity: Equipe::class, inversedBy: 'joueurs')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Equipe $equipe = null;

    public function getEquipe(): ?Equipe
    {
        return $this->equipe;
    }

    public function setEquipe(?Equipe $equipe): self
    {
        $this->equipe = $equipe;
        return $this;
    }
}
