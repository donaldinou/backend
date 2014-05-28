<?php

namespace Viteloge\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Campaign
 *
 * @ORM\Table(name="campaign")
 * @ORM\Entity(repositoryClass="Viteloge\AdminBundle\Entity\CampaignRepository")
 */
class Campaign
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="template", type="text")
     */
    private $template;

    /**
     * @ORM\Column(name="subject", type="string",length=128)
     */
    private $subject;

    /**
     * @var string
     *
     * @ORM\Column(name="transaction", type="string", length=1,nullable=true)
     */
    private $transaction;

    /**
     * @var array
     *
     * @ORM\Column(name="types", type="simple_array",nullable=true)
     */
    private $types;

    /**
     * @var array
     *
     * @ORM\Column(name="pieces", type="simple_array",nullable=true)
     */
    private $pieces;

    /**
     * @var string
     *
     * @ORM\Column(name="insee", type="string", length=5, nullable=true)
     */
    private $insee;

    /**
     * @ORM\OneToMany(targetEntity="CampaignSchedule",mappedBy="campaign",cascade={"persist", "merge","remove"})
     */
    protected $schedules;
    
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
     * Set name
     *
     * @param string $name
     * @return Campaign
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set template
     *
     * @param string $template
     * @return Campaign
     */
    public function setTemplate($template)
    {
        $this->template = $template;
    
        return $this;
    }

    /**
     * Get template
     *
     * @return string 
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * Set transaction
     *
     * @param string $transaction
     * @return Campaign
     */
    public function setTransaction($transaction)
    {
        $this->transaction = $transaction;
    
        return $this;
    }

    /**
     * Get transaction
     *
     * @return string 
     */
    public function getTransaction()
    {
        return $this->transaction;
    }

    /**
     * Set types
     *
     * @param array $types
     * @return Campaign
     */
    public function setTypes($types)
    {
        $this->types = $types;
    
        return $this;
    }

    /**
     * Get types
     *
     * @return array 
     */
    public function getTypes()
    {
        return $this->types;
    }

    /**
     * Set pieces
     *
     * @param array $pieces
     * @return Campaign
     */
    public function setPieces($pieces)
    {
        $this->pieces = $pieces;
    
        return $this;
    }

    /**
     * Get pieces
     *
     * @return array 
     */
    public function getPieces()
    {
        return $this->pieces;
    }

    /**
     * Set insee
     *
     * @param string $insee
     * @return Campaign
     */
    public function setInsee($insee)
    {
        $this->insee = $insee;
    
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
     * Constructor
     */
    public function __construct()
    {
        $this->schedules = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Add schedules
     *
     * @param \Viteloge\AdminBundle\Entity\CampaignSchedule $schedules
     * @return Campaign
     */
    public function addSchedule(\Viteloge\AdminBundle\Entity\CampaignSchedule $schedules)
    {
        $this->schedules[] = $schedules;
        $schedules->setCampaign( $this );
    
        return $this;
    }

    /**
     * Remove schedules
     *
     * @param \Viteloge\AdminBundle\Entity\CampaignSchedule $schedules
     */
    public function removeSchedule(\Viteloge\AdminBundle\Entity\CampaignSchedule $schedules)
    {
        $this->schedules->removeElement($schedules);
    }

    /**
     * Get schedules
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSchedules()
    {
        return $this->schedules;
    }

    /**
     * Set subject
     *
     * @param string $subject
     * @return Campaign
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    
        return $this;
    }

    /**
     * Get subject
     *
     * @return string 
     */
    public function getSubject()
    {
        return $this->subject;
    }
}
