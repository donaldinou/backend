<?php

namespace Viteloge\AdminBundle\Entity;


use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Table(name="cities")
 * @ORM\Entity
 */
class City
{
    /**
     * @ORM\Column(name="country",type="string",length=2)
     */
    private $country;
    /**
     * @ORM\Column(name="geonameid",type="integer")
     * @ORM\Id
     */
    private $geonameid;
    /**
     * @ORM\Column(name="name",type="string",length=200)
     */
    private $name;
    /**
     * @ORM\Column(name="asciiName",type="string",length=200)
     */
    private $asciiName;
    /**
     * @ORM\Column(name="latitude",type="decimal",precision=8,scale=5)
     */
    private $latitude;
    /**
     * @ORM\Column(name="longitude",type="decimal",precision=8,scale=5)
     */
    private $longitude;
    /**
     * @ORM\Column(name="population",type="integer")
     */
    private $population;
    /**
     * @ORM\Column(name="alternateNames",type="string",length=5000)
     */
    private $alternateNames;
    /**
     * @ORM\Column(name="zipcode",type="string",length=10)
     */
    private $zipcode;
    
}
