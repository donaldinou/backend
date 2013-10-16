<?php

namespace Viteloge\AdminBundle\Entity;

class CityFilter 
{
    protected $ville_id;
    protected $ville;

    public function getVilleId()
    {
        return $this->ville_id;
    }
    public function setVilleId( $ville_id )
    {
        $this->ville_id = $ville_id;
        return $this;
    }
    public function getVille()
    {
        return $this->ville;
    }
    public function setVille( $ville )
    {
        $this->ville = $ville;
        return $this;
    }
}