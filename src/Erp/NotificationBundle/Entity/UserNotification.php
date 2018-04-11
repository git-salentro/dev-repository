<?php

namespace Erp\NotificationBundle\Entity;

use Erp\CoreBundle\Entity\DatesAwareTrait;
use Erp\CoreBundle\Entity\DatesAwareInterface;
use Doctrine\ORM\Mapping as ORM;
use Erp\PropertyBundle\Entity\Property;
use Doctrine\Common\Collections\ArrayCollection;

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
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Notifications", mappedBy="userNotification", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $notifications;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Alerts", mappedBy="userNotification", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $alerts;

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

    public function __construct()
    {
        $this->notifications = new ArrayCollection();
        $this->alerts = new ArrayCollection();
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
     * Add notification
     *
     * @param \Erp\NotificationBundle\Entity\Notifications $notification
     *
     * @return UserNotification
     */
    public function addNotification(\Erp\NotificationBundle\Entity\Notifications $notification)
    {
        $notification->setUserNotification($this);
        $this->notifications[] = $notification;

        return $this;
    }

    /**
     * Remove notification
     *
     * @param \Erp\NotificationBundle\Entity\Notifications $notification
     */
    public function removeNotification(\Erp\NotificationBundle\Entity\Notifications $notification)
    {
        $this->notifications->removeElement($notification);
    }

    /**
     * Get notifications
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getNotifications()
    {
        return $this->notifications;
    }

    /**
     * Add alert
     *
     * @param \Erp\NotificationBundle\Entity\Alerts $alert
     *
     * @return UserNotification
     */
    public function addAlert(\Erp\NotificationBundle\Entity\Alerts $alert)
    {
        $alert->setUserNotification($this);
        $this->alerts[] = $alert;

        return $this;
    }

    /**
     * Remove alert
     *
     * @param \Erp\NotificationBundle\Entity\Alerts $alert
     */
    public function removeAlert(\Erp\NotificationBundle\Entity\Alerts $alert)
    {
        $this->alerts->removeElement($alert);
    }

    /**
     * Get alerts
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAlerts()
    {
        return $this->alerts;
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
