<?php

namespace App\Entity;

use App\Repository\FormationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FormationRepository::class)]
class Formation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name_ref = null;

    #[ORM\Column(length: 255)]
    private ?string $num_siret = null;

    #[ORM\Column(length: 255)]
    private ?string $domain = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $webSite = null;

    #[ORM\OneToMany(mappedBy: 'userFormation', targetEntity: User::class)]
    private Collection $users;

     #[ORM\OneToMany(mappedBy: 'publicationFormation', targetEntity: Publication::class)]
    private Collection $publications;

     #[ORM\Column(type: Types::TEXT)]
     private ?string $description = null;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->publications = new ArrayCollection();
        $this->name_ref = "A modifier";
        $this->domain = "A modifier";
        $this->webSite = "A compléter ultérieurement";
        $this->description = "A compléter ultérieurement";
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNameRef(): ?string
    {
        return $this->name_ref;
    }

    public function setNameRef(string $name_ref): self
    {
        $this->name_ref = $name_ref;

        return $this;
    }

    public function getNumSiret(): ?string
    {
        return $this->num_siret;
    }

    public function setNumSiret(string $num_siret): self
    {
        $this->num_siret = $num_siret;

        return $this;
    }

    public function getDomain(): ?string
    {
        return $this->domain;
    }

    public function setDomain(string $domain): self
    {
        $this->domain = $domain;

        return $this;
    }

    public function getWebSite(): ?string
    {
        return $this->webSite;
    }

    public function setWebSite(string $webSite): self
    {
        $this->webSite = $webSite;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->setUserFormation($this);
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
            $publication->setPublicationFormation($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getUserFormation() === $this) {
                $user->setUserFormation(null);
            }
        }
        
        return $this;
    }

    public function removePublication(Publication $publication): self
    {
        if ($this->publications->removeElement($publication)) {
            // set the owning side to null (unless already changed)
            if ($publication->getPublicationFormation() === $this) {
                $publication->setPublicationFormation(null);
            }
        }

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }
}
