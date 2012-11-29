<?php

namespace Viteloge\AdminBundle\Service;

class TestTraitementService 
{
    private $traitement;
    
    public function __construct( $traitement)
    {
        $this->traitement = $traitement;
    }

    public function run( $type, $source )
    {}
    
}
