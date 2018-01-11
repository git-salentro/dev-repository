<?php

namespace Erp\PaymentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class StripeRecurringPayment
 *
 * @ORM\Table(name="stripe_recurring_payment")
 * @ORM\Entity(repositoryClass="Erp\PaymentBundle\Repository\StripeRecurringPaymentRepository")
 * @ORM\HasLifecycleCallbacks
 */
class StripeRecurringPayment
{
    const TYPE_SINGLE = 'single';
    const TYPE_RECURRING = 'recurring';
    const STATUS_PENDING = 'pending';
    const STATUS_FAILURE = 'failure';
    const STATUS_SUCCESS = 'success';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var StripeCustomer
     *
     * @ORM\ManyToOne(targetEntity="StripeCustomer", inversedBy="recurringPayments")
     * @ORM\JoinColumn(name="customer_id", referencedColumnName="id")
     */
    private $customer;

    /**
     * @var StripeAccount
     *
     * @ORM\ManyToOne(targetEntity="StripeAccount", inversedBy="recurringPayments")
     * @ORM\JoinColumn(name="account_id", referencedColumnName="id")
     */
    private $account;

    /**
     * @var float
     *
     * @ORM\Column(name="amount", type="float", scale=2, nullable=true)
     */
    private $amount;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", columnDefinition="ENUM('single','recurring')", nullable=true)
     */
    private $type = self::TYPE_RECURRING;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", columnDefinition="ENUM('pending', 'failure', 'success')", nullable=true)
     */
    private $status = self::STATUS_PENDING;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="start_payment_at", type="date", nullable=true)
     */
    private $startPaymentAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="next_payment_at", type="date", nullable=true)
     */
    private $nextPaymentAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime")
     */
    private $updatedAt;

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
    public function preUpdate()
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
     * Set amount
     *
     * @param float $amount
     *
     * @return StripeRecurringPayment
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return StripeRecurringPayment
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @return StripeRecurringPayment
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
     * Set startPaymentAt
     *
     * @param \DateTime $startPaymentAt
     *
     * @return StripeRecurringPayment
     */
    public function setStartPaymentAt($startPaymentAt)
    {
        $this->startPaymentAt = $startPaymentAt;

        return $this;
    }

    /**
     * Get startPaymentAt
     *
     * @return \DateTime
     */
    public function getStartPaymentAt()
    {
        return $this->startPaymentAt;
    }

    /**
     * Set nextPaymentAt
     *
     * @param \DateTime $nextPaymentAt
     *
     * @return StripeRecurringPayment
     */
    public function setNextPaymentAt($nextPaymentAt)
    {
        $this->nextPaymentAt = $nextPaymentAt;

        return $this;
    }

    /**
     * Get nextPaymentAt
     *
     * @return \DateTime
     */
    public function getNextPaymentAt()
    {
        return $this->nextPaymentAt;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return StripeRecurringPayment
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return StripeRecurringPayment
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set customer
     *
     * @param \Erp\PaymentBundle\Entity\StripeCustomer $customer
     *
     * @return StripeRecurringPayment
     */
    public function setCustomer(\Erp\PaymentBundle\Entity\StripeCustomer $customer = null)
    {
        $this->customer = $customer;

        return $this;
    }

    /**
     * Get customer
     *
     * @return \Erp\PaymentBundle\Entity\StripeCustomer
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * Set account
     *
     * @param \Erp\PaymentBundle\Entity\StripeAccount $account
     *
     * @return StripeRecurringPayment
     */
    public function setAccount(\Erp\PaymentBundle\Entity\StripeAccount $account = null)
    {
        $this->account = $account;

        return $this;
    }

    /**
     * Get account
     *
     * @return \Erp\PaymentBundle\Entity\StripeAccount
     */
    public function getAccount()
    {
        return $this->account;
    }

    public function isRecurring()
    {
        return $this->type === self::TYPE_RECURRING;
    }
}
