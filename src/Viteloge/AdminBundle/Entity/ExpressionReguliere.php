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
     * @ORM\Column(name="IdExpression", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    /**
     * @ORM\OneToOne(targetEntity="Traitement",inversedBy="expression")
     * @ORM\JoinColumn(name="idTraitement",referencedColumnName="IdTraitement")
     */
    private $traitement;
    
    /**
     * @ORM\Column(type="string",length=255)
     */
    private $ExpLiensFiche = '';
    /**
     * @ORM\Column(type="string",length=255)
     */
    private $ExpAnnonceList = '';
    /**
     * @ORM\Column(type="string",length=255)
     */
    private $ExpNbPage = '';
    /**
     * @ORM\Column(type="string",length=255)
     */
    private $ExpPageSuivante = '';
    /**
     * @ORM\Column(type="string",length=255)
     */
    private $ExpUrlElements = '';
    /**
     * @ORM\Column(type="string",length=255)
     */
    private $ExpTypeLogement = '';
    /**
     * @ORM\Column(type="string",length=255)
     */
    private $ExpNbChambre = '';
    /**
     * @ORM\Column(type="string",length=255)
     */
    private $ExpUrlPhoto = '';
    /**
     * @ORM\Column(type="string",length=255)
     */
    private $ExpNbBien = '';
    /**
     * @ORM\Column(type="string",length=255)
     */
    private $ExpSurface = '';
    /**
     * @ORM\Column(type="string",length=255)
     */
    private $ExpPiece = '';
    /**
     * @ORM\Column(type="string",length=255)
     */
    private $ExpPrix = '';
    /**
     * @ORM\Column(type="string",length=255)
     */
    private $ExpVille = '';
    /**
     * @ORM\Column(type="string",length=255)
     */
    private $ExpArrondissement = '';
    /**
     * @ORM\Column(type="string",length=255)
     */
    private $ExpCP = '';
    /**
     * @ORM\Column(type="string",length=255)
     */
    private $ExpDescription = '';
    /**
     * @ORM\Column(type="string",length=255)
     */
    private $ExpAgence = '';
    /**
     * @ORM\Column(type="string",length=255)
     */
    private $ExpIgnoreAgence = '';
    /**
     * @ORM\Column(type="string",length=255)
     */
    private $SplitResultAnnonce = '';
    
    

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
        if ( is_null( $value ) && ! is_null( $this->$property ) ) {
            $value = '';
        }
        $this->$property = $value;
    }

    
}
