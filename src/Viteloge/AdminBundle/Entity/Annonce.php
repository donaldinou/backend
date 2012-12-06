<?php

namespace Viteloge\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Viteloge\AdminBundle\Entity\Annonce
 *
 * @ORM\Table(name="annonce")
 * @ORM\Entity(repositoryClass="Viteloge\AdminBundle\Entity\AnnonceRepository")
 */
class Annonce
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="idAnnonce", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="Flag",type="integer")
     */
    private $flag;

    /**
     * @ORM\Column(name="dateSuppression",type="datetime")
     */
    private $dateSuppression;
    
    /**
     * @ORM\ManyToOne(targetEntity="Traitement",inversedBy="blacklists")
     * @ORM\JoinColumn(name="IdTraitement",referencedColumnName="IdTraitement")
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
