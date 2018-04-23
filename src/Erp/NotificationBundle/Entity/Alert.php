<?php

namespace Erp\NotificationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Alert
 *
 * @ORM\Table(name="erp_notification_alert")
 * @ORM\Entity
 */
class Alert
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
     * @ORM\Column(name="days_after", type="integer")
     */
    private $daysAfter;

    /**
<<<<<<< HEAD
     * @ORM\ManyToOne(targetEntity="UserNotification", inversedBy="Alert", cascade={"persist"})
=======
     * @ORM\ManyToOne(targetEntity="UserNotification", inversedBy="alerts", cascade={"persist"})
>>>>>>> 82c23d89e64f286f5525204e66251b600b4568c4
     * @ORM\JoinColumn(name="user_notification_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $userNotification;

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
     * Set daysAfter
     *
     * @param integer $daysAfter
     *
     * @return Alert
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
     * Set userNotification
     *
     * @param \Erp\NotificationBundle\Entity\UserNotification $userNotification
     *
     * @return Alert
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
}
