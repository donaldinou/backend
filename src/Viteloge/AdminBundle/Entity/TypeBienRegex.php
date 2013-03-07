<?php

namespace Viteloge\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="type_bien_regex")
 * @ORM\Entity()
 */
class TypeBienRegex
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @ORM\ManyToOne(targetEntity="TypeBien",inversedBy="regexs")
     * @ORM\JoinColumn(name="code",referencedColumnName="codeType")
     */
    private $code;

    /**
     * @ORM\Column(type="integer")
     */
    private $score;
    /**
     * @ORM\Column(type="string")
     */
    private $expression;
    /**
     * @ORM\Column(type="integer")
     */
    private $rank;
    

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
        return join( "-", array( $this->code, substr( $this->expression, 0, 30 ) ) );
    }
    

}
