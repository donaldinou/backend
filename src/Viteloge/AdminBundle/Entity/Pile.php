<?php

namespace Viteloge\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Viteloge\AdminBundle\Entity\Pile
 *
 * @ORM\Table(name="pile")
 * @ORM\Entity(repositoryClass="Viteloge\AdminBundle\Entity\PileRepository")
 */
class Pile
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="idPile", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;


    /**
     * @ORM\ManyToOne(targetEntity="Traitement",inversedBy="blacklists")
     * @ORM\JoinColumn(name="IdTraitement",referencedColumnName="IdTraitement")
     */
    private $traitement;

    /**
     * @ORM\Column(name="UrlPile",type="string",length=255)
     */
    private $url;
    /**
     * @ORM\Column(name="UrlPile2",type="string",length=255)
     */
    private $url2;
    /**
     * @ORM\Column(name="UrlTypePile",type="string",length=1)
     */
    private $type;
    /**
     * @ORM\Column(name="StatutPile",type="integer")
     */
    private $statut;
    /**
     * @ORM\Column(name="NextPageTraitement",type="integer")
     */
    private $nextPageTraitement;
    /**
     * @ORM\Column(name="TimeStampPile",type="datetime")
     */
    private $timestamp;
    /**
     * @ORM\Column(name="Tentatives",type="integer")
     */
    private $tentatives;
    /**
     * @ORM\Column(name="ProcessusName",type="string",length=10)
     */
    private $processusName;
    
    
    
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

    public function getFull_Url()
    {
        return $this->url . $this->url2;
    }
    

}
