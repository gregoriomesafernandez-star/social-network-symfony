<?php

namespace App\Entity;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

//use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'users')]

// Validación alternativa sin AJAX:
//#[UniqueEntity(fields: ['email'], message: 'Ya hay un email con esta dirección')] 
//#[UniqueEntity(fields: ['nick'], message: 'Ya hay un nick igual')]

class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $role = null;

    #[ORM\Column(length: 255, nullable: true, unique: true)]
    #[Assert\NotBlank(message: "El email {{ value }} no puede estar vacío")]
    #[Assert\Email(message: "El email no es válido")]
    private ?string $email = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotBlank(message: "El nombre no puede estar vacío")]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotBlank(message: "Los apellidos no puede estar vacío")]
    private ?string $surname = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotBlank(message: "La contraseña no puede estar vacío")]
    private ?string $password = null;

    #[ORM\Column(length: 50, nullable: true, unique: true)]
    #[Assert\NotBlank(message: "El nick no puede estar vacío")]
    private ?string $nick = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $bio = null;

    #[ORM\Column(length: 2, nullable: true)]
    private ?string $active = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Following::class, cascade: ['remove'])]
    private Collection $following;

    #[ORM\OneToMany(mappedBy: 'followed', targetEntity: Following::class, cascade: ['remove'])]
    private Collection $followers;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Like::class, cascade: ['remove'])]
    private Collection $likes;

    public function __construct()
    {
        $this->following = new ArrayCollection();
        $this->followers = new ArrayCollection();
        $this->likes = new ArrayCollection();
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function eraseCredentials(): void
    {

    }

    public function getRoles(): array
    {
        $roles = $this->roles ?? [];
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }


    public function __toString()
    {
        return $this->name;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }
    public function setRole(?string $role): self
    {
        $this->role = $role;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }
    public function setEmail(?string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }
    public function setName(?string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getSurname(): ?string
    {
        return $this->surname;
    }
    public function setSurname(?string $surname): self
    {
        $this->surname = $surname;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }
    public function setPassword(?string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function getNick(): ?string
    {
        return $this->nick;
    }
    public function setNick(?string $nick): self
    {
        $this->nick = $nick;
        return $this;
    }

    public function getBio(): ?string
    {
        return $this->bio;
    }
    public function setBio(?string $bio): self
    {
        $this->bio = $bio;
        return $this;
    }

    public function getActive(): ?string
    {
        return $this->active;
    }
    public function setActive(?string $active): self
    {
        $this->active = $active;
        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }
    public function setImage(?string $image): self
    {
        $this->image = $image;
        return $this;
    }

    public function getFollowing(): Collection
    {
        return $this->following;
    }

    public function getFollowers(): Collection
    {
        return $this->followers;
    }

    public function getLikes(): Collection
    {
        return $this->likes;
    }

}
