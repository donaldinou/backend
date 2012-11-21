<?php

namespace Viteloge\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Viteloge\AdminBundle\Entity\Traitement
 *
 * @ORM\Table(name="traitement")
 * @ORM\Entity(repositoryClass="Viteloge\AdminBundle\Entity\TraitementRepository")
 */
class Traitement
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="IdTraitement", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="ExpressionReguliere",mappedBy="traitement")
     */
    private $expression;

    /**
     * @ORM\ManyToOne(targetEntity="Agence",inversedBy="traitements")
     * @ORM\JoinColumn(name="IdAgence", referencedColumnName="idAgence")
     */
    private $agence;
    
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
        if ( property_exists( $this->expression, $property ) ) {
            $this->expression->$property = $value;
        }
        $this->$property = $value;
    }
}
