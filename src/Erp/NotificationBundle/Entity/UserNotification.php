<?php

namespace Erp\NotificationBundle\Entity;

use Erp\CoreBundle\Entity\DatesAwareTrait;
use Erp\CoreBundle\Entity\DatesAwareInterface;
use Doctrine\ORM\Mapping as ORM;
use Erp\PropertyBundle\Entity\Property;

/**
 * Class UserNotification
 *
 * @ORM\Table(name="erp_notifications_user_notification")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class UserNotification implements DatesAwareInterface
{
    use DatesAwareTrait;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="days_before", type="integer")
     */
    private $daysBefore;

    /**
     * @var integer
     *
     * @ORM\Column(name="days_after", type="integer")
     */
    private $daysAfter;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_send_alert_automatically", type="integer")
     */
    private $sendAlertAutomatically;

    /**
     * @var Property
     *
     * @ORM\OneToOne(targetEntity="\Erp\PropertyBundle\Entity\Property", inversedBy="userNotification")
     */
    private $property;

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
     * Set daysBefore
     *
     * @param integer $daysBefore
     *
     * @return UserNotification
     */
    public function setDaysBefore($daysBefore)
    {
        $this->daysBefore = $daysBefore;

        return $this;
    }

    /**
     * Get daysBefore
     *
     * @return integer
     */
    public function getDaysBefore()
    {
        return $this->daysBefore;
    }

    /**
     * Set daysAfter
     *
     * @param integer $daysAfter
     *
     * @return UserNotification
     */
    public function setDaysAfter($daysAfter)
    {
        $this->daysAfter = $daysAfter;

        return $this;
    }

    /**
     * Get daysAfter
     *
     * @return integer
     */
    public function getDaysAfter()
    {
        return $this->daysAfter;
    }

    /**
     * Set sendAlertAutomatically
     *
     * @param integer $sendAlertAutomatically
     *
     * @return UserNotification
     */
    public function setSendAlertAutomatically($sendAlertAutomatically)
    {
        $this->sendAlertAutomatically = $sendAlertAutomatically;

        return $this;
    }

    /**
     * Get sendAlertAutomatically
     *
     * @return integer
     */
    public function getSendAlertAutomatically()
    {
        return $this->sendAlertAutomatically;
    }

    /**
     * Set property
     *
     * @param \Erp\PropertyBundle\Entity\Property $property
     *
     * @return UserNotification
     */
    public function setProperty(\Erp\PropertyBundle\Entity\Property $property = null)
    {
        $this->property = $property;

        return $this;
    }

    /**
     * Get property
     *
     * @return \Erp\PropertyBundle\Entity\Property
     */
    public function getProperty()
    {
        return $this->property;
    }
}
