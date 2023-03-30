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
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $adress = null;

    #[ORM\Column(length: 255)]
    private ?string $postalCode = null;

    #[ORM\Column(length: 255)]
    private ?string $city = null;

    #[ORM\Column(length: 255)]
    private ?string $tel = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $firstName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $profession = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $status = null;

    #[ORM\ManyToMany(targetEntity: Competence::class, inversedBy: 'users')]
    private Collection $userCompetence;

    #[ORM\ManyToMany(targetEntity: Diplome::class, inversedBy: 'users')]
    private Collection $UserDiplome;

    #[ORM\ManyToOne(inversedBy: 'users')]
    private ?Company $userEntreprise = null;

    #[ORM\ManyToOne(inversedBy: 'users')]
    private ?Formation $userFormation = null;

    #[ORM\ManyToOne(inversedBy: 'users')]
    private ?Apply $userApply = null;
    
    #[ORM\OneToMany(mappedBy: 'publicationUser', targetEntity: Publication::class)]
    private Collection $publications;

    public function __construct()
    {
        $this->userCompetence = new ArrayCollection();
        $this->UserDiplome = new ArrayCollection();
        $this->publications = new ArrayCollection();
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

    public function getAdress(): ?string
    {
        return $this->adress;
    }

    public function setAdress(string $adress): self
    {
        $this->adress = $adress;

        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(string $postalCode): self
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getTel(): ?string
    {
        return $this->tel;
    }

    public function setTel(string $tel): self
    {
        $this->tel = $tel;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getProfession(): ?string
    {
        return $this->profession;
    }

    public function setProfession(?string $profession): self
    {
        $this->profession = $profession;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection<int, Competence>
     */
    public function getUserCompetence(): Collection
    {
        return $this->userCompetence;
    }

    public function addUserCompetence(Competence $userCompetence): self
    {
        if (!$this->userCompetence->contains($userCompetence)) {
            $this->userCompetence->add($userCompetence);
        }
    }

    /**
     * @return Collection<int, Publication>
     */
     
    public function getPublications(): Collection
    {
        return $this->publications;
    }

    public function addPublication(Publication $publication): self
    {
        if (!$this->publications->contains($publication)) {
            $this->publications->add($publication);
            $publication->setPublicationUser($this);
        }

        return $this;
    }

    public function removeUserCompetence(Competence $userCompetence): self
    {
        $this->userCompetence->removeElement($userCompetence);

        return $this;
    }

    /**
     * @return Collection<int, Diplome>
     */
     
    public function getUserDiplome(): Collection
    {
        return $this->UserDiplome;
    }

    public function addUserDiplome(Diplome $userDiplome): self
    {
        if (!$this->UserDiplome->contains($userDiplome)) {
            $this->UserDiplome->add($userDiplome);
        }
    }

    public function removePublication(Publication $publication): self
    {
        if ($this->publications->removeElement($publication)) {
            // set the owning side to null (unless already changed)
            if ($publication->getPublicationUser() === $this) {
                $publication->setPublicationUser(null);
            }
        }

        return $this;
    }

    public function removeUserDiplome(Diplome $userDiplome): self
    {
        $this->UserDiplome->removeElement($userDiplome);

        return $this;
    }

    public function getUserEntreprise(): ?Company
    {
        return $this->userEntreprise;
    }

    public function setUserEntreprise(?Company $userEntreprise): self
    {
        $this->userEntreprise = $userEntreprise;

        return $this;
    }

    public function getUserFormation(): ?Formation
    {
        return $this->userFormation;
    }

    public function setUserFormation(?Formation $userFormation): self
    {
        $this->userFormation = $userFormation;

        return $this;
    }

    public function getUserApply(): ?Apply
    {
        return $this->userApply;
    }

    public function setUserApply(?Apply $userApply): self
    {
        $this->userApply = $userApply;

        return $this;
    }

}
