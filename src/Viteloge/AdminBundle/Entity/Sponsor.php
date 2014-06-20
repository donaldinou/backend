<?php

namespace Viteloge\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Viteloge\AdminBundle\Entity\Sponsor
 *
 * @ORM\Table(name="sponsor")
 * @ORM\Entity()
 */
class Sponsor{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="idSponsor", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="typeSponsor",type="string",length=20)
     */
    private $type;

    /**
     * @ORM\Column(name="inseeSponsor",type="string",length=255)
     */
    private $insee;
    /**
     * @ORM\Column(name="transacSponsor",type="string",length=1)
     */
    private $transac;
    /**
     * @ORM\Column(name="titreSponsor",type="string",length=50)
     */
    private $titre;
    /**
     * @ORM\Column(name="texteSponsor",type="string",length=100)
     */
    private $texte;
    /**
     * @ORM\Column(name="urlSponsor",type="string",length=255)
     */
    private $url;
    /**
     * @ORM\Column(name="linkSponsor",type="string",length=255)
     */
    private $link;
    

    public function __construct() {
        foreach ( array( "insee", "transac" ) as $field ) {
            $this->$field = "";
        }
    }
    

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
     * Set type
     *
     * @param string $type
     * @return Sponsor
     */
    public function setType($type)
    {
        $this->type = $this->nonNull( $type );
    
        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set insee
     *
     * @param string $insee
     * @return Sponsor
     */
    public function setInsee($insee)
    {
        $this->insee = $this->nonNull( $insee );
    
        return $this;
    }

    /**
     * Get insee
     *
     * @return string 
     */
    public function getInsee()
    {
        return $this->insee;
    }

    /**
     * Set transac
     *
     * @param string $transac
     * @return Sponsor
     */
    public function setTransac($transac)
    {
        $this->transac = $this->nonNull( $transac );
    
        return $this;
    }

    /**
     * Get transac
     *
     * @return string 
     */
    public function getTransac()
    {
        return $this->transac;
    }

    /**
     * Set titre
     *
     * @param string $titre
     * @return Sponsor
     */
    public function setTitre($titre)
    {
        $this->titre = $titre;
    
        return $this;
    }

    /**
     * Get titre
     *
     * @return string 
     */
    public function getTitre()
    {
        return $this->titre;
    }

    /**
     * Set texte
     *
     * @param string $texte
     * @return Sponsor
     */
    public function setTexte($texte)
    {
        $this->texte = $texte;
    
        return $this;
    }

    /**
     * Get texte
     *
     * @return string 
     */
    public function getTexte()
    {
        return $this->texte;
    }

    /**
     * Set url
     *
     * @param string $url
     * @return Sponsor
     */
    public function setUrl($url)
    {
        $this->url = $url;
    
        return $this;
    }

    /**
     * Get url
     *
     * @return string 
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set link
     *
     * @param string $link
     * @return Sponsor
     */
    public function setLink($link)
    {
        $this->link = $link;
    
        return $this;
    }

    /**
     * Get link
     *
     * @return string 
     */
    public function getLink()
    {
        return $this->link;
    }

    private function nonNull( $var ) {
        if ( is_null( $var ) ) {
            return "";
        }
        return $var;
    }
}
