<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\GameRepository")
 */
class Game
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", mappedBy="jeux")
     */
    private $games;

    public function __construct()
    {
        $this->games = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getGames(): Collection
    {
        return $this->games;
    }

    public function addGame(User $game): self
    {
        if (!$this->games->contains($game)) {
            $this->games[] = $game;
            $game->addJeux($this);
        }

        return $this;
    }

    public function removeGame(User $game): self
    {
        if ($this->games->contains($game)) {
            $this->games->removeElement($game);
            $game->removeJeux($this);
        }

        return $this;
    }
}
