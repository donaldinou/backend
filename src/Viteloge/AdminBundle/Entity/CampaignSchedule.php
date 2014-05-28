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
     * @ORM\ManyToOne(targetEntity="Campaign",inversedBy="schedules")
     * @ORM\JoinColumn(name="campaign_id", referencedColumnName="id")
     */
    private $campaign;


    public function __construct() {
        $t = time();
        $t = $t - ( $t % ( 60*60 ) ) + 2*60*60;
        $this->sendAt = new \DateTime( "@" . $t, new \DateTimeZone( 'Europe/Paris') );
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
}