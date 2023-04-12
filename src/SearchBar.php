<?php

namespace App;

class SearchBar
{
    private string $searchBar = "";

    public function getSearchBar(): ?string
    {
        return $this->searchBar;
    }

    public function setSearchBar(string $searchBar): self
    {
        $this->searchBar = $searchBar;
        return $this;
    }

    public function __toString()
    {
        return $this->searchBar;
    }

}