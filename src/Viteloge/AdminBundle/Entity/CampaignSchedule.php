<?php

namespace Viteloge\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CampaignSchedule
 *
 * @ORM\Table(name="campaign_schedules")
 * @ORM\Entity
 */
class CampaignSchedule
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
     * @var \DateTime
     *
     * @ORM\Column(name="send_at", type="date")
     */
    private $sendAt;

    /**
     * @ORM\Column(name="is_anniversary",type="boolean")
     */
    private $isAnniversary;
    
    /**
     * @ORM\Column(name="subscribed_since",type="integer",nullable=true)
     */
    private $subscribedSince;
    
    /**
     * @ORM\ManyToOne(targetEntity="Campaign",inversedBy="schedules")
     * @ORM\JoinColumn(name="campaign_id", referencedColumnName="id")
     */
    private $campaign;


    public function __construct() {
        $t = time();
        $t = $t - ( $t % ( 60*60 ) ) + 2*60*60;
        $this->sendAt = new \DateTime( "@" . $t, new \DateTimeZone( 'Europe/Paris') );
        $this->isAnniversary = false;
        $this->subscribedSince = null;
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
     * Set sendAt
     *
     * @param \DateTime $sendAt
     * @return CampaignSchedule
     */
    public function setSendAt($sendAt)
    {
        $this->sendAt = $sendAt;
    
        return $this;
    }

    /**
     * Get sendAt
     *
     * @return \DateTime 
     */
    public function getSendAt()
    {
        return $this->sendAt;
    }

    /**
     * Set campaign
     *
     * @param \Viteloge\AdminBundle\Entity\Campaign $campaign
     * @return CampaignSchedule
     */
    public function setCampaign(\Viteloge\AdminBundle\Entity\Campaign $campaign = null)
    {
        $this->campaign = $campaign;
    
        return $this;
    }

    /**
     * Get campaign
     *
     * @return \Viteloge\AdminBundle\Entity\Campaign 
     */
    public function getCampaign()
    {
        return $this->campaign;
    }

    /**
     * Set isAnniversary
     *
     * @param boolean $isAnniversary
     * @return CampaignSchedule
     */
    public function setIsAnniversary($isAnniversary)
    {
        $this->isAnniversary = $isAnniversary;

        if ( ! $this->isAnniversary ) {
            $this->setSubscribedSince( null );
        }
        
        return $this;
    }

    /**
     * Get isAnniversary
     *
     * @return boolean 
     */
    public function getIsAnniversary()
    {
        return $this->isAnniversary;
    }

    /**
     * Set subscribedSince
     *
     * @param integer $subscribedSince
     * @return CampaignSchedule
     */
    public function setSubscribedSince($subscribedSince)
    {
        $this->subscribedSince = $subscribedSince;
    
        return $this;
    }

    /**
     * Get subscribedSince
     *
     * @return integer 
     */
    public function getSubscribedSince()
    {
        return $this->subscribedSince;
    }
}
