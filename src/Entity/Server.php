<?php

namespace App\Entity;

use App\Repository\ServerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ServerRepository::class)]
class Server
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $create_at = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $modified_at = null;

    #[ORM\OneToOne(inversedBy: 'server', cascade: ['persist', 'remove'])]
    private ?Login $login = null;

    #[ORM\Column(length: 255)]
    private ?string $directory_path = null;

    #[ORM\OneToOne(mappedBy: 'server', cascade: ['persist', 'remove'])]
    private ?Config $config = null;

    #[ORM\Column(nullable: true)]
    private ?int $pid = null;

    public function __construct()
    {
        $this->configs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getCreateAt(): ?\DateTimeInterface
    {
        return $this->create_at;
    }

    public function setCreateAt(\DateTimeInterface $create_at): static
    {
        $this->create_at = $create_at;

        return $this;
    }

    public function getModifiedAt(): ?\DateTimeInterface
    {
        return $this->modified_at;
    }

    public function setModifiedAt(\DateTimeInterface $modified_at): static
    {
        $this->modified_at = $modified_at;

        return $this;
    }

    public function getLogin(): ?Login
    {
        return $this->login;
    }

    public function setLogin(?Login $login): static
    {
        $this->login = $login;

        return $this;
    }

    public function getDirectoryPath(): ?string
    {
        return $this->directory_path;
    }

    public function setDirectoryPath(?string $directory_path): static
    {
        $this->directory_path = $directory_path;

        return $this;
    }

    public function getConfig(): ?Config
    {
        return $this->config;
    }

    public function setConfig(?Config $config): static
    {
        // unset the owning side of the relation if necessary
        if ($config === null && $this->config !== null) {
            $this->config->setServer(null);
        }

        // set the owning side of the relation if necessary
        if ($config !== null && $config->getServer() !== $this) {
            $config->setServer($this);
        }

        $this->config = $config;

        return $this;
    }

    public function getPid(): ?int
    {
        return $this->pid;
    }

    public function setPid(?int $pid): static
    {
        $this->pid = $pid;

        return $this;
    }

}
