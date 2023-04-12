<?php

namespace App;

class SearchData
{
    private string $searchBar = "";
    private array $types = [];

    public function getSearchBar(): ?string
    {
        return $this->searchBar;
    }

    public function setSearchBar(string $searchBar): self
    {
        $this->searchBar = $searchBar;
        return $this;
    }

    public function getTypes(): ?array
    {
        return $this->types;
    }

    public function setTypes($types): self
    {
        $this->types = $types;
        return $this;
    }

    public function __toString()
    {
        return $this->searchBar;
    }

}