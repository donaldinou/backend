<?php

namespace Viteloge\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Viteloge\AdminBundle\Entity\Cycle
 *
 * @ORM\Table(name="cycle")
 * @ORM\Entity
 */
class Cycle
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="idCycle", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Traitement",inversedBy="cycles")
     * @ORM\JoinColumn(name="idTraitement",referencedColumnName="IdTraitement")
     */
    private $traitement;
    /**
     * @ORM\ManyToOne(targetEntity="XmlFeed",inversedBy="cycles")
     * @ORM\JoinColumn(name="idXmlFeed",referencedColumnName="idXmlFeed")
     */
    private $xmlfeed;
    

    /**
     * @ORM\Column(name="debut",type="datetime")
     */
    private $debut;
    /**
     * @ORM\Column(name="fin",type="datetime")
     */
    private $fin;
    /**
     * @ORM\Column(name="nbAnnonce",type="integer")
     */
    private $nbAnnonce;
    /**
     * @ORM\Column(name="nbAnnonceInsert",type="integer")
     */
    private $nbAnnonceInsert;
    /**
     * @ORM\Column(name="nbAnnonceDelete",type="integer")
     */
    private $nbAnnonceDelete;
    
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

    
    
}
