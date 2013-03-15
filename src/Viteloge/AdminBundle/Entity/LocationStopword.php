<?php

namespace Viteloge\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="location_stopword")
 * @ORM\Entity()
 */
class LocationStopword
{
    /**
     * @ORM\Column(name="expression",type="string",length=128)
     * @ORM\Id
     */
    private $expression;

    /**
     * Methode magique __set()
     */
    public function __set($property, $value)
    {
        $this->$property = $value;
    }
    public function __isset($name)
    {
        return property_exists($this, $name);
    }
    /**
     * Methode magique __get()
     */
    public function __get($property)
    {
        return $this->$property;
    }

    public function __toString()
    {
        return $this->libelle;
    }

}
