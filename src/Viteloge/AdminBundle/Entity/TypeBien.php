<?php

namespace Viteloge\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="type_bien")
 * @ORM\Entity()
 */
class TypeBien
{
    /**
     * @ORM\Column(name="codeType",type="string",length=1)
     * @ORM\Id
     */
    private $code;

    /**
     * @ORM\Column(name="libelleType",type="string",length=50)
     */
    private $libelle;


    /**
     * @ORM\OneToMany(targetEntity="TypeBienRegex",mappedBy="code")
     */
    private $regexs;

    
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
