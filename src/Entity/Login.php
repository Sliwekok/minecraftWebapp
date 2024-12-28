<?php

    namespace App\Entity;

    use App\Repository\LoginRepository;
    use Doctrine\ORM\Mapping as ORM;
    use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
    use Symfony\Component\Security\Core\User\UserInterface;

    #[ORM\Entity(repositoryClass: LoginRepository::class)]
    class Login implements UserInterface, PasswordAuthenticatedUserInterface
    {
        #[ORM\Id]
        #[ORM\GeneratedValue]
        #[ORM\Column]
        private ?int $id = null;

        #[ORM\Column(length: 180, unique: true)]
        private ?string $username = null;

        #[ORM\Column]
        private array $roles = ['default'];

        #[ORM\Column]
        private ?string $email = null;

        #[ORM\Column]
        private int $is_active = 1;


        /**
         * @var string The hashed password
         */
        #[ORM\Column]
        private ?string $password = null;

        #[ORM\OneToOne(mappedBy: 'login', cascade: ['persist', 'remove'])]
        private ?Server $server = null;

        public function getId(): ?int
        {
            return $this->id;
        }

        public function getUsername(): ?string
        {
            return $this->username;
        }

        public function setUsername(string $username): static
        {
            $this->username = $username;

            return $this;
        }

        /**
         * A visual identifier that represents this user.
         *
         * @see UserInterface
         */
        public function getUserIdentifier(): string
        {
            return (string) $this->username;
        }

        /**
         * @see UserInterface
         */
        public function getRoles(): array
        {
            $roles = $this->roles;
            // guarantee every user at least has ROLE_USER
            $roles[] = 'ROLE_USER';

            return array_unique($roles);
        }

        public function setRoles(array $roles): static
        {
            $this->roles = $roles;

            return $this;
        }

        /**
         * @see PasswordAuthenticatedUserInterface
         */
        public function getPassword(): string
        {
            return $this->password;
        }

        public function setPassword(string $password): static
        {
            $this->password = $password;

            return $this;
        }

        /**
         * @see UserInterface
         */
        public function eraseCredentials(): void
        {
            // If you store any temporary, sensitive data on the user, clear it here
            // $this->plainPassword = null;
        }

        public function setIsActive(int $isActive): static
        {
            $this->is_active = $isActive;

            return $this;
        }

        public function getIsActive(): int
        {
            return $this->is_active;
        }

        public function setEmail(string $email): static
        {
            $this->email = $email;

            return $this;
        }

        public function getEmail(): string
        {
            return $this->email;
        }

        public function getServer(): ?Server
        {
            return $this->server;
        }

        public function setServer(?Server $server): static
        {
            // unset the owning side of the relation if necessary
            if ($server === null && $this->server !== null) {
                $this->server->setLogin(null);
            }

            // set the owning side of the relation if necessary
            if ($server !== null && $server->getLogin() !== $this) {
                $server->setLogin($this);
            }

            $this->server = $server;

            return $this;
        }

    }
