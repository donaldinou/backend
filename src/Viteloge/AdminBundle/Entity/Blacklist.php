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
}
