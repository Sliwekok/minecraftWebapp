<?php

namespace App\Entity;

use App\Repository\ConfigRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ConfigRepository::class)]
class Config
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $port = null;

    #[ORM\Column]
    private ?int $max_ram = null;

    #[ORM\Column]
    private ?bool $pvp = null;

    #[ORM\Column]
    private ?bool $hardcore = null;

    #[ORM\Column]
    private ?int $max_players = null;

    #[ORM\Column]
    private ?bool $whitelist = null;

    #[ORM\Column(length: 255)]
    private ?string $difficulty = null;

    #[ORM\Column]
    private ?bool $allow_flight = null;

    #[ORM\Column(length: 255)]
    private ?string $seed = null;

    #[ORM\OneToOne(inversedBy: 'config', cascade: ['persist', 'remove'])]
    private ?Server $server = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPort(): ?int
    {
        return $this->port;
    }

    public function setPort(int $port): static
    {
        $this->port = $port;

        return $this;
    }

    public function getMaxRam(): ?int
    {
        return $this->max_ram;
    }

    public function setMaxRam(int $max_ram): static
    {
        $this->max_ram = $max_ram;

        return $this;
    }

    public function isPvp(): ?bool
    {
        return $this->pvp;
    }

    public function setPvp(bool $pvp): static
    {
        $this->pvp = $pvp;

        return $this;
    }

    public function isHardcore(): ?bool
    {
        return $this->hardcore;
    }

    public function setHardcore(bool $hardcore): static
    {
        $this->hardcore = $hardcore;

        return $this;
    }

    public function getMaxPlayers(): ?int
    {
        return $this->max_players;
    }

    public function setMaxPlayers(int $max_players): static
    {
        $this->max_players = $max_players;

        return $this;
    }

    public function isWhitelist(): ?bool
    {
        return $this->whitelist;
    }

    public function setWhitelist(bool $whitelist): static
    {
        $this->whitelist = $whitelist;

        return $this;
    }

    public function getDifficulty(): ?string
    {
        return $this->difficulty;
    }

    public function setDifficulty(string $difficulty): static
    {
        $this->difficulty = $difficulty;

        return $this;
    }

    public function isAllowFlight(): ?bool
    {
        return $this->allow_flight;
    }

    public function setAllowFlight(bool $allow_flight): static
    {
        $this->allow_flight = $allow_flight;

        return $this;
    }

    public function getSeed(): ?string
    {
        return $this->seed;
    }

    public function setSeed(string $seed): static
    {
        $this->seed = $seed;

        return $this;
    }

    public function getServer(): ?Server
    {
        return $this->server;
    }

    public function setServer(?Server $server): static
    {
        $this->server = $server;

        return $this;
    }
}
