<?php

namespace Viteloge\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Viteloge\AdminBundle\Entity\Agence
 *
 * @ORM\Table(name="agence")
 * @ORM\Entity(repositoryClass="Viteloge\AdminBundle\Entity\AgenceRepository")
 */
class Agence
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="idAgence", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="specifAgence",type="string",length=255)
     */
    private $specif;
    /**
     * @ORM\Column(name="nomAgence",type="string",length=255)
     */
    private $nom;
    /**
     * @ORM\Column(name="mailAgence",type="string",length=255)
     */
    private $mail;
    /**
     * @ORM\Column(name="adresseAgence",type="string",length=255)
     */
    private $adresse;
    /**
     * @ORM\Column(name="cpAgence",type="string",length=5)
     */
    private $cp;
    /**
     * @ORM\Column(name="villeAgence",type="string",length=50)
     */
    private $ville;
    /**
     * @ORM\Column(name="telAgence",type="string",length=15)
     */
    private $tel;
    /**
     * @ORM\Column(name="faxAgence",type="string",length=15)
     */
    private $fax;
    /**
     * @ORM\Column(name="urlAgence",type="string",length=255)
     */
    private $url;
    /**
     * @ORM\Column(name="civiliteContactAgence",type="string",length=20)
     */
    private $civiliteContact;
    /**
     * @ORM\Column(name="nomContactAgence",type="string",length=100)
     */
    private $nomContact;
    /**
     * @ORM\Column(name="dptAgence",type="string",length=255)
     */
    private $departement;
    /**
     * @ORM\Column(name="nbAnnonceAgence",type="integer")
     */
    private $nbAnnonce;
    /**
     * @ORM\Column(name="dateCreation",type="date")
     */
    private $dateCreation;
    /**
     * @ORM\Column(name="idPrivilege",type="integer",nullable=true)
     */
    private $idPrivilege;
    /**
     * @ORM\Column(name="inactive",type="boolean")
     */
    private $inactive;

    /**
     * @ORM\OneToMany(targetEntity="Privilege", mappedBy="agence")
     */
    private $privileges;

    /**
     * @ORM\OneToMany(targetEntity="Traitement", mappedBy="agence")
     */
    private $traitements;

    /**
     * @ORM\OneToMany(targetEntity="XmlFeed", mappedBy="agence")
     */
    private $xml_feeds;


    
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * Methode magique __get()
     */
    public function __get($property)
    {
        return $this->$property;
    }

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

    public function __toString() 
    {
        return $this->nom;
    }

    public function getCountTraitements()
    {
        return count( $this->traitements );
    }
    public function getActive() 
    {
        return ! $this->inactive;
    }
}
