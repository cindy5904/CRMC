<?php

namespace App\Entity;

use App\Repository\CompanyRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CompanyRepository::class)]
class Company
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name_ref = null;

    #[ORM\Column(length: 255)]
    private ?string $num_siret = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $domaine = null;

    #[ORM\Column(length: 255)]
    private ?string $logo = null;

    #[ORM\Column(length: 255)]
    private ?string $partenaire = null;

    #[ORM\Column(length: 255)]
    private ?string $web_site = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDomaine(): ?string
    {
        return $this->domaine;
    }

    public function setDomaine(string $domaine): self
    {
        $this->domaine = $domaine;

        return $this;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(string $logo): self
    {
        $this->logo = $logo;

        return $this;
    }

    public function getPartenaire(): ?string
    {
        return $this->partenaire;
    }

    public function setPartenaire(string $partenaire): self
    {
        $this->partenaire = $partenaire;

        return $this;
    }

    public function getWebSite(): ?string
    {
        return $this->web_site;
    }

    public function setWebSite(string $web_site): self
    {
        $this->web_site = $web_site;

        return $this;
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
}
