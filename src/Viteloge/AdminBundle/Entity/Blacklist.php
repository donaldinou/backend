<?php

namespace Viteloge\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Viteloge\AdminBundle\Entity\Blacklist
 *
 * @ORM\Table(name="blacklist")
 * @ORM\Entity(repositoryClass="Viteloge\AdminBundle\Entity\BlacklistRepository")
 */
class Blacklist
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="idBlacklist", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="raison",type="string",length=255)
     */
    private $raison;

    /**
     * @ORM\Column(name="blacklisted_at",type="datetime")
     */
    private $when;


    /**
     * @ORM\ManyToOne(targetEntity="Traitement",inversedBy="blacklists")
     * @ORM\JoinColumn(name="idTraitement",referencedColumnName="IdTraitement")
     */
    private $traitement;
    

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
