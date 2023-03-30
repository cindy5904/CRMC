<?php

namespace App\Entity;

use App\Repository\PublicationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PublicationRepository::class)]
class Publication
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;

    #[ORM\ManyToOne(inversedBy: 'publications')]
    private ?User $publicationUser = null;

    #[ORM\ManyToOne(inversedBy: 'publications')]
    private ?Company $publicationCompany = null;

    #[ORM\ManyToOne(inversedBy: 'publications')]
    private ?Formation $publicationFormation = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

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

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getPublicationUser(): ?User
    {
        return $this->publicationUser;
    }

    public function setPublicationUser(?User $publicationUser): self
    {
        $this->publicationUser = $publicationUser;

        return $this;
    }

    public function getPublicationCompany(): ?Company
    {
        return $this->publicationCompany;
    }

    public function setPublicationCompany(?Company $publicationCompany): self
    {
        $this->publicationCompany = $publicationCompany;

        return $this;
    }

    public function getPublicationFormation(): ?Formation
    {
        return $this->publicationFormation;
    }

    public function setPublicationFormation(?Formation $publicationFormation): self
    {
        $this->publicationFormation = $publicationFormation;

        return $this;
    }
}
