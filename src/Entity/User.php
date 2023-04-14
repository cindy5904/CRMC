<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\Nullable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'Il existe déjà un compte avec cet email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\NotBlank, Assert\Email]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank, Assert\Length(min: 2)]
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
    private Collection $userDiplome;

    #[ORM\ManyToOne(inversedBy: 'users')]
    private ?Company $userEntreprise = null;

    #[ORM\ManyToOne(inversedBy: 'users')]
    private ?Formation $userFormation = null;
    
    #[ORM\OneToMany(mappedBy: 'publicationUser', targetEntity: Publication::class)]
    private Collection $publications;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $logo = null;
    
    /**
     * @ORM\Column(type="blob", nullable=true)
     */
    private $cv;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Apply::class)]
    private Collection $applies;


    public function __construct()
    {  
        $this->createdAt = new \DateTimeImmutable();
        $this->userCompetence = new ArrayCollection();
        $this->userDiplome = new ArrayCollection();
        $this->publications = new ArrayCollection();
        $this->adress = 'A modifier';
        $this->city = 'A modifier';
        $this->postalCode = 'A modifier';
        $this->tel = 'A compléter ultérieurement';
        $this->applies = new ArrayCollection();
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

        return $this;
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
        return $this->userDiplome;
    }

    public function addUserDiplome(Diplome $userDiplome): self
    {
        if (!$this->userDiplome->contains($userDiplome)) {
            $this->userDiplome->add($userDiplome);
        }

        return $this;
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
        $this->userDiplome->removeElement($userDiplome);

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

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(?string $logo): self
    {
        $this->logo = $logo;

        return $this;
    }

    public function getCv()
    {
        return $this->cv;
    }

    public function setCv($cv)
    {
        $this->cv = $cv;

        return $this;
    }

    /**
     * @return Collection<int, Apply>
     */
    public function getApplies(): Collection
    {
        return $this->applies;
    }

    public function addApply(Apply $apply): self
    {
        if (!$this->applies->contains($apply)) {
            $this->applies->add($apply);
            $apply->setUser($this);
        }

        return $this;
    }

    public function removeApply(Apply $apply): self
    {
        if ($this->applies->removeElement($apply)) {
            // set the owning side to null (unless already changed)
            if ($apply->getUser() === $this) {
                $apply->setUser(null);
            }
        }

        return $this;
    }
}
