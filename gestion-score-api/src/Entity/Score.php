<?php

namespace App\Entity;

use App\Repository\ScoreRepository;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: ScoreRepository::class)]
class Score
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["score:read"])]
    private ?int $id = null;

    #[ORM\OneToOne()]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    #[Groups(["score:read"])]
    private ?Equipe $equipeA = null;

    #[ORM\Column(length: 70)]
    #[Groups(["score:read"])]
    private ?string $score = null;

    #[ORM\OneToOne()]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    #[Groups(["score:read"])]
    private ?Equipe $equipeB = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEquipeA(): ?Equipe
    {
        return $this->equipeA;
    }

    public function setEquipeA(Equipe $equipeA): static
    {
        $this->equipeA = $equipeA;

        return $this;
    }

    public function getEquipeB(): ?Equipe
    {
        return $this->equipeB;
    }

    public function setEquipeB(Equipe $equipeB): static
    {
        $this->equipeB = $equipeB;

        return $this;
    }

    public function getScore(): ?string
    {
        return $this->score;
    }

    public function setScore(string $score): static
    {
        $this->score = $score;

        return $this;
    }
}
