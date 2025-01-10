<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\Collection; 
use Doctrine\Common\Collections\ArrayCollection; 
use App\Entity\Book;  

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private $username;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(type: 'json')]
    private array $roles = [];

    // Relation Many-to-Many avec Book
    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Book", inversedBy="users")
     * @ORM\JoinTable(name="users_books")
     */
    private Collection $books;

    public function __construct()
    {
        $this->books = new ArrayCollection();
    }

    public function getBooks(): Collection
    {
        return $this->books;
    }

    public function addBook(Book $book): self
    {
        if (!$this->books->contains($book)) {
            $this->books[] = $book;
        }

        return $this;
    }

    public function removeBook(Book $book): self
    {
        $this->books->removeElement($book);

        return $this;
    }
    
    // Implémentation de getUserIdentifier() qui fait partie de l'interface UserInterface
    public function getUserIdentifier(): string
    {
        return (string) $this->email;  // Utilisation de l'email comme identifiant unique
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    // Méthodes des rôles, mot de passe, etc.

    public function getRoles(): array
    {
        // Si les rôles de base ne sont pas définis, on ajoute 'ROLE_USER'
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';  // Chaque utilisateur doit avoir le rôle de base 'ROLE_USER'
    
        return array_unique($roles);  // On retourne un tableau unique des rôles
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }


    public function eraseCredentials(): void
    {
        // Effacement des données sensibles si nécessaire
    }
}
