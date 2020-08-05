<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Table(name="`user`")
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=75, unique=true)
     */
    private $username;

    /**
     * @ORM\OneToMany(targetEntity=Todo::class, mappedBy="user", orphanRemoval=true)
     */
    private $todo;

    public function __construct()
    {
        $this->todo = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }
    /**
     * A visual identifier that represents this user.
     * @see UserInterface
     */
    public function getEmail(): ?string
    {
        return (string) $this->email;
    }
    /**
     * @see UserInterface
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }


    public function getUsername(): string
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

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return Collection|Todo[]
     */
    public function getTodo(): Collection
    {
        return  $this->todo;
    }

    public function addTodo(Todo $todo): self
    {
        if (!$this->todo->contains($todo)) {
            $this->todo[] = $todo;
            $todo->setUser($this);
        }

        return $this;
    }

    public function removeTodo(Todo $todo): self
    {
        if ($this->todo->contains($todo)) {
            $this->todo->removeElement($todo);
            // set the owning side to null (unless already changed)
            if ($todo->getUser() === $this) {
                $todo->setUser(null);
            }
        }

        return $this;
    }
}
