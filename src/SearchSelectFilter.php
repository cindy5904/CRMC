<?php 

namespace App;

class SearchSelectFilter
{
    private $types = "";
    private $profession = "";

    public function getTypes()
    {
        return $this->types;
    }

    public function setTypes($types)
    {
        $this->types = $types;
        return $this;
    }

    public function __toString()
    {
        return $this->types;
    }

    public function getProfession()
    {
        return $this->profession;
    }

    public function setProfession($profession)
    {
        $this->profession = $profession;
        return $this;
    }
}