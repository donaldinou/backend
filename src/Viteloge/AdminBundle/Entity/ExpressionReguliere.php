<?php

namespace Viteloge\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Viteloge\AdminBundle\Entity\ExpressionReguliere
 *
 * @ORM\Table(name="expressionreguliere")
 * @ORM\Entity
 */
class ExpressionReguliere
{
    /**
     * @ORM\Id
     * @ORM\OneToOne(targetEntity="Traitement",inversedBy="expression")
     * @ORM\JoinColumn(name="IdTraitement",referencedColumnName="IdTraitement")
     */
    private $traitement;
    
    /**
     * @ORM\Column(type="string",length=255)
     */
    private $ExpLiensFiche;

    /**
     * Methode magique __get()
     */
    public function __get($property)
    {
        return $this->$property;
    }
    /**
     * Methode magique __isset()
     */
    public function __isset($name)
    {
        return property_exists($this, $name);
    }
    /**
     * Methode magique __set()
     */
    public function __set($property, $value)
    {
        $this->$property = $value;
    }

    
}
