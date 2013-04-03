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
     * @ORM\Column(name="IdAnnonce", type="integer")
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
     * @ORM\Column(name="Flag",type="integer")
     */
    private $flag;

    /**
     * @ORM\Column(name="DateInsertion",type="datetime")
     */
    private $dateInsertion;
    /**
     * @ORM\Column(name="DateUpdate",type="datetime")
     */
    private $dateUpdate;
    /**
     * @ORM\Column(name="DateSuppression",type="datetime")
     */
    private $dateSuppression;
    

    /**
     * @ORM\Column(name="Url",type="string",length=255)
     */
    private $url;
    /**
     * @ORM\Column(name="TypeTransaction",type="string",length=1)
     */
    private $typeTransaction;
    /**
     * @ORM\Column(name="TypeLogement",type="string",length=50)
     */
    private $typeLogement;
    

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
