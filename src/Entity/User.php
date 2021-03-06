<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    private $email;

    #[ORM\Column(type: 'json')]
    private $roles = [];

    #[ORM\Column(type: 'string')]
    private $password;

    #[ORM\Column(type: 'string', length: 100)]
    private $name;

    #[ORM\ManyToMany(targetEntity: Product::class, inversedBy: 'users')]
    private $favoritesProducts;

    #[ORM\ManyToMany(targetEntity: Product::class, inversedBy: 'excludedUsers')]
    #[ORM\JoinTable(name: 'user_excluded')]
    private $ExcludedProducts;

    public function __construct()
    {
        $this->favoritesProducts = new ArrayCollection();
        $this->ExcludedProducts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->email;
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

    public function setRoles(array $roles): self
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

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection|Product[]
     */
    public function getFavoritesProducts(): Collection
    {
        return $this->favoritesProducts;
    }

    public function addFavoritesProduct(Product $favoritesProduct): self
    {
        if (!$this->favoritesProducts->contains($favoritesProduct)) {
            $this->favoritesProducts[] = $favoritesProduct;
        }

        return $this;
    }

    public function removeFavoritesProduct(Product $favoritesProduct): self
    {
        $this->favoritesProducts->removeElement($favoritesProduct);

        return $this;
    }

    /**
     * @return Collection|Product[]
     */
    public function getExcludedProducts(): Collection
    {
        return $this->ExcludedProducts;
    }

    public function addExcludedProduct(Product $excludedProduct): self
    {
        if (!$this->ExcludedProducts->contains($excludedProduct)) {
            $this->ExcludedProducts[] = $excludedProduct;
        }

        return $this;
    }

    public function removeExcludedProduct(Product $excludedProduct): self
    {
        $this->ExcludedProducts->removeElement($excludedProduct);

        return $this;
    }
}
