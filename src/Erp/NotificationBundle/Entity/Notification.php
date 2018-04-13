<?php

namespace Erp\NotificationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Notification
 *
 * @ORM\Table(name="erp_notification_notification")
 * @ORM\Entity
 */
class Notification
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
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
     * @ORM\ManyToOne(targetEntity="UserNotification", inversedBy="Notification", cascade={"persist"})
     * @ORM\JoinColumn(name="user_notification_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $userNotification;

    /**
     * @var Template
     *
     * @ORM\ManyToOne(targetEntity="Template")
     * @ORM\JoinColumn(name="template_id", referencedColumnName="id")
     */
    private $template;

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
     * @return Notification
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
     * Set userNotification
     *
     * @param \Erp\NotificationBundle\Entity\UserNotification $userNotification
     *
     * @return Notification
     */
    public function setUserNotification(\Erp\NotificationBundle\Entity\UserNotification $userNotification = null)
    {
        $this->userNotification = $userNotification;

        return $this;
    }

    /**
     * Get userNotification
     *
     * @return \Erp\NotificationBundle\Entity\UserNotification
     */
    public function getUserNotification()
    {
        return $this->userNotification;
    }

    /**
     * Set template
     *
     * @param \Erp\NotificationBundle\Entity\Template $template
     *
     * @return Notification
     */
    public function setTemplate(\Erp\NotificationBundle\Entity\Template $template = null)
    {
        $this->template = $template;

        return $this;
    }

    /**
     * Get template
     *
     * @return \Erp\NotificationBundle\Entity\Template
     */
    public function getTemplate()
    {
        return $this->template;
    }
}
