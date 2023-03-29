<?php

namespace App\Entity;

use App\Repository\FormationRepository;
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
}
