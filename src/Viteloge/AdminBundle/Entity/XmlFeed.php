<?php

namespace Viteloge\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Viteloge\AdminBundle\Entity\XmlFeed
 *
 * @ORM\Table(name="xml_feed")
 * @ORM\Entity(repositoryClass="Viteloge\AdminBundle\Entity\XmlFeedRepository")
 */
class XmlFeed
{
    public static $Modules = array( 'AC3', 'ERA', 'trovit', 'adaptimmo' );
    
    /**
     * @var integer $id
     *
     * @ORM\Column(name="idXmlFeed", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;


    /**
     * @ORM\Column(name="idXmlFeedMap", type="integer") 
     */
    private $idXmlFeedMap = 0;

    /**
     * @ORM\Column(type="boolean")
     */
    private $ismap;

    /**
     * @ORM\ManyToOne(targetEntity="Agence",inversedBy="xml_feeds")
     * @ORM\JoinColumn(name="IdAgence", referencedColumnName="idAgence")
     */
    private $agence;

    /**
     * @ORM\Column(type="string",length=255)
     */
    private $url ;
    /**
     * @ORM\Column(type="string",length=50,nullable=false)
     */
    private $module = '';
    /**
     * @ORM\Column(type="string",length=1)
     */
    private $transaction;
    /**
     * ORM\Column(type="datatime")
     */
    private $dateLastDownload;
    /**
     * @ORM\Column(type="boolean")
     */
    private $inactif;

    

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

    public function __toString()
    {
        if ( $this->url ) 
        {
            return $this->url;
        }
        return "";
    }

    public function getNomAgence()
    {
        if ( isset( $this->agence ) ) {
            return $this->agence->nom;
        }
        return '';
    }

    public function getActif()
    {
        return ! $this->inactif;
    }
    
}
