<?php

namespace Viteloge\AdminBundle\Entity;

class ExpressionUnderTest 
{
    private $name;
    private $value;
    private $is_array;

    private $result;
    
    public function __construct( $name, $traitement, $array = false ) 
    {
        $this->name = $name;
        $this->is_array = $array;
        if ( ! empty( $traitement->$name ) ) {
            $this->value = $traitement->$name;
        }
    }
    public function isValid() 
    {
        return ! is_null( $this->value );
    }

    public function getValue()
    {
        return $this->value;
    }

    public function isArray(){
        return $this->is_array;
    }
    
    
}
