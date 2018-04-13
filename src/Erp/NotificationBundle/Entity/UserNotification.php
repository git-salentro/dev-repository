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
 * @ORM\Table(name="erp_notification_user_notification")
 * @ORM\Entity(repositoryClass="Erp\NotificationBundle\Repository\UserNotificationRepository")
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
     * @ORM\OneToMany(targetEntity="Notification", mappedBy="userNotification", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $notifications;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Alert", mappedBy="userNotification", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $alerts;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_send_alert_automatically", type="integer")
     */
    private $sendAlertAutomatically;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Erp\PropertyBundle\Entity\Property")
     * @ORM\JoinTable(
     *     name="erp_notification_user_notification_property",
     *     joinColumns={@ORM\JoinColumn(name="user_notification_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="property_id", referencedColumnName="id")}
     * )
     */
    private $properties;

    public function __construct()
    {
        $this->notifications = new ArrayCollection();
        $this->alerts = new ArrayCollection();
        $this->properties = new ArrayCollection();
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    /**
     * @ORM\PreUpdate
     */
    public function setUpdatedDate()
    {
        $this->updatedAt = new \DateTime();
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
     * @param \Erp\NotificationBundle\Entity\Notification $notification
     *
     * @return UserNotification
     */
    public function addNotification(\Erp\NotificationBundle\Entity\Notification $notification)
    {
        $notification->setUserNotification($this);
        $this->notifications[] = $notification;

        return $this;
    }

    /**
     * Remove notification
     *
     * @param \Erp\NotificationBundle\Entity\Notification $notification
     */
    public function removeNotification(\Erp\NotificationBundle\Entity\Notification $notification)
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
     * @param \Erp\NotificationBundle\Entity\Alert $alert
     *
     * @return UserNotification
     */
    public function addAlert(\Erp\NotificationBundle\Entity\Alert $alert)
    {
        $alert->setUserNotification($this);
        $this->alerts[] = $alert;

        return $this;
    }

    /**
     * Remove alert
     *
     * @param \Erp\NotificationBundle\Entity\Alert $alert
     */
    public function removeAlert(\Erp\NotificationBundle\Entity\Alert $alert)
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
     * Add property
     *
     * @param \Erp\PropertyBundle\Entity\Property $property
     *
     * @return UserNotification
     */
    public function addProperty(\Erp\PropertyBundle\Entity\Property $property)
    {
        $this->properties[] = $property;

        return $this;
    }

    /**
     * Remove property
     *
     * @param \Erp\PropertyBundle\Entity\Property $property
     */
    public function removeProperty(\Erp\PropertyBundle\Entity\Property $property)
    {
        $this->properties->removeElement($property);
    }

    /**
     * Get properties
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProperties()
    {
        return $this->properties;
    }
}
