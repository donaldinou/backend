<?php

namespace Viteloge\AdminBundle\Entity;

class BlacklistFilter 
{
    protected $poliris;
    protected $sort;

    public function getPoliris()
    {
        return $this->poliris;
    }
    public function setPoliris( $new_poliris )
    {
        $this->poliris = $new_poliris;
    }
    public function getSort()
    {
        return $this->sort;
    }
    public function setSort( $new_sort )
    {
        $this->sort = $new_sort;
    }

    public function __construct()
    {
        $this->poliris = 0;
        $this->sort = TraitementRepository::SORT_BY_PRIVILEGE;
    }
    
}