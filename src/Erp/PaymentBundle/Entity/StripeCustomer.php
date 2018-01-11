<?php

namespace Erp\PaymentBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Erp\UserBundle\Entity\User;

/**
 * Class StripeCustomer
 *
 * @ORM\Table(name="stripe_customer")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class StripeCustomer
{
    const BANK_ACCOUNT = 'ba';
    const CREDIT_CARD = 'cc';
    const BILLING_AUTOMATICALLY = 'charge_automatically';
    const BILLING_SEND_INVOICE = 'send_invoice';
    const BILLING_DAYS_UNTIL_DUE = 5;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var User
     *
     * @ORM\OneToOne(targetEntity="Erp\UserBundle\Entity\User", inversedBy="stripeCustomers", cascade={"persist"})
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $user;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="StripeRecurringPayment", mappedBy="customer", cascade={"persist"})
     */
    private $recurringPayments;

    /**
     * @var string
     *
     * @ORM\Column(name="customer_id", type="string")
     */
    private $customerId;

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
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Erp\PaymentBundle\Entity\StripeSubscription", mappedBy="stripeCustomer", cascade={"ALL"})
     * @ORM\OrderBy({"createdAt"="DESC"})
     */
    protected $stripeSubscriptions;

    public function __construct()
    {
        $this->stripeSubscriptions = new ArrayCollection();
        $this->recurringPayments = new ArrayCollection();
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
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set customerId
     *
     * @param string $customerId
     *
     * @return StripeCustomer
     */
    public function setCustomerId($customerId)
    {
        $this->customerId = $customerId;

        return $this;
    }

    /**
     * Get customerId
     *
     * @return string
     */
    public function getCustomerId()
    {
        return $this->customerId;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return StripeCustomer
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
     * @return StripeCustomer
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
     * Set user
     *
     * @param \Erp\UserBundle\Entity\User $user
     *
     * @return StripeCustomer
     */
    public function setUser(\Erp\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Erp\UserBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Add stripeSubscription
     *
     * @param \Erp\PaymentBundle\Entity\StripeSubscription $stripeSubscription
     *
     * @return StripeCustomer
     */
    public function addStripeSubscription(\Erp\PaymentBundle\Entity\StripeSubscription $stripeSubscription)
    {
        $stripeSubscription->setStripeCustomer($this);
        $this->stripeSubscriptions[] = $stripeSubscription;

        return $this;
    }

    /**
     * Remove stripeSubscription
     *
     * @param \Erp\PaymentBundle\Entity\StripeSubscription $stripeSubscription
     */
    public function removeStripeSubscription(\Erp\PaymentBundle\Entity\StripeSubscription $stripeSubscription)
    {
        $this->stripeSubscriptions->removeElement($stripeSubscription);
    }

    /**
     * Get stripeSubscriptions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getStripeSubscriptions()
    {
        return $this->stripeSubscriptions;
    }
}
