<?php

namespace Viteloge\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Viteloge\AdminBundle\Entity\Privilege
 *
 * @ORM\Table(name="privilege")
 * @ORM\Entity(repositoryClass="Viteloge\AdminBundle\Entity\PrivilegeRepository")
 */
class Privilege
{
    public static $PRIVILEGE_CODES = array(
        "0100000" => 'Référencement "Précense"',
        "0110000" => 'Référencement "Basic"',
        "0111000" => 'Référencement "Plus"',
        "1110000" => 'Mise en valeur "Basic"',
        "1111000" => 'Mise en valeur "Plus"',
        "1111100" => 'Mise en valeur "Premium"'
    );
    
    public static $TYPES_TRANSACTION = array(
        '' => 'Toutes',
        'L' => 'Location',
        'V' => 'Vente'
    );
    

    /**
     * @var integer $id
     *
     * @ORM\Column(name="idPrivilege", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Agence",inversedBy="privileges")
     * @ORM\JoinColumn(name="idAgence", referencedColumnName="idAgence")
     */
    private $agence;

    /**
     * @ORM\Column(name="dateDebut",type="date",nullable=true)
     */
    private $dateDebut;
    /**
     * @ORM\Column(name="dateFin",type="date",nullable=true)
     */
    private $dateFin;
    /**
     * @ORM\Column(name="specifAgence",type="string",length=255,nullable=true)
     */
    private $specifAgence;
    /**
     * @ORM\Column(name="typeTransaction",type="string",length=1,nullable=true)
     */
    private $typeTransaction;
    /**
     * @ORM\Column(name="minPrix",type="float",nullable=true)
     */
    private $minPrix;
    /**
     * @ORM\Column(name="maxPrix",type="float",nullable=true)
     */
    private $maxPrix;
    /**
     * @ORM\Column(name="keywords",type="string",length=255)
     */
    private $keywords = '';
    /**
     * @ORM\Column(name="nbAnnonce",type="integer",nullable=true)
     */
    private $nbAnnonce;
    /**
     * @ORM\Column(name="code",type="string",length=10)
     */
    private $code;
    
    
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

    public function getNomAgence()
    {
        if ( isset( $this->agence ) ) {
            return $this->agence->nom;
        } else {
            return '';
        }
    }

    public function getOffre()
    {
        $offre = self::$PRIVILEGE_CODES[$this->code];
        return preg_replace( '/"([^"]*)"/', '<strong>${1}</strong>', $offre );
    }
    
    public function getNombreDAnnonces()
    {
        return $this->nbAnnonce;
    }
    

}
