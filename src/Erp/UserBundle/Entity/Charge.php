<?php

namespace Erp\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Erp\CoreBundle\Entity\DatesAwareInterface;
use Erp\CoreBundle\Entity\DatesAwareTrait;

/**
 * Charge
 *
 * @ORM\Table(name="charges")
 * @ORM\Entity(repositoryClass="ChargeRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Charge implements DatesAwareInterface
{
    use DatesAwareTrait;

    const STATUS_PAID = 'paid';
    const STATUS_PENDING = 'pending';
    const STATUS_FAILURE = 'failure';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="guid")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string")
     */
    protected $status = self::STATUS_PENDING;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_recurring", type="boolean")
     */
    protected $recurring = false;

    /**
     * @var string
     *
     * @ORM\Column(name="amount", type="string", length=255)
     */
    protected $amount;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    protected $description;

    /**
     * @var User Manager
     * @ORM\ManyToOne(targetEntity="Erp\UserBundle\Entity\User", inversedBy="chargeOutgoings")
     */
    protected $manager; //sender

    /**
     * @var User Landlord
     * @ORM\ManyToOne(targetEntity="Erp\UserBundle\Entity\User", inversedBy="chargeIncomings")
     */
    protected $landlord; //receiver

    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        $this->createdAt = new \DateTime();
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function preUpdate()
    {
        $this->updatedAt = new \DateTime();
    }

    /**
     * Get id
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @return Charge
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set amount
     *
     * @param string $amount
     *
     * @return Charge
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return string
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Charge
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set manager
     *
     * @param \Erp\UserBundle\Entity\User $manager
     *
     * @return Charge
     */
    public function setManager(\Erp\UserBundle\Entity\User $manager = null)
    {
        $this->manager = $manager;

        return $this;
    }

    /**
     * Get manager
     *
     * @return \Erp\UserBundle\Entity\User
     */
    public function getManager()
    {
        return $this->manager;
    }

    /**
     * Set landlord
     *
     * @param \Erp\UserBundle\Entity\User $landlord
     *
     * @return Charge
     */
    public function setLandlord(\Erp\UserBundle\Entity\User $landlord = null)
    {
        $this->landlord = $landlord;

        return $this;
    }

    /**
     * Get landlord
     *
     * @return \Erp\UserBundle\Entity\User
     */
    public function getLandlord()
    {
        return $this->landlord;
    }

    /**
     * @return bool
     */
    public function isPaid()
    {
        return $this->status === self::STATUS_PAID;
    }

    /**
     * @return bool
     */
    public function isRecurring()
    {
        return $this->recurring;
    }

    /**
     * Set recurring
     *
     * @param bool $recurring
     *
     * @return Charge
     */
    public function setRecurring($recurring)
    {
        $this->recurring = $recurring;

        return $this;
    }

    /**
     * Get recurring
     *
     * @return bool
     */
    public function getRecurring()
    {
        return $this->recurring;
    }
}
