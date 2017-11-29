<?php

namespace Erp\PaymentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class StripeRecurringPayment
 *
 * @ORM\Table(name="stripe_recurring_payment")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class StripeRecurringPayment
{
    const BASE_PLAN_ID = 'base_yearly_plan';
    const DEFAULT_CURRENCY = 'usd';
    const DEFAULT_INTERVAL = 'year';

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
     * @ORM\ManyToOne(
     *     targetEntity="Erp\PaymentBundle\Entity\StripeCustomer",
     *     inversedBy="stripeRecurringPayments"
     * )
     * @ORM\JoinColumn(name="stripe_customer_id", referencedColumnName="id")
     */
    private $stripeCustomer;

    /**
     * @var string
     *
     * @ORM\Column(name="subscription_id", type="string")
     */
    private $subscriptionId;

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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set subscriptionId
     *
     * @param string $subscriptionId
     *
     * @return StripeRecurringPayment
     */
    public function setSubscriptionId($subscriptionId)
    {
        $this->subscriptionId = $subscriptionId;

        return $this;
    }

    /**
     * Get subscriptionId
     *
     * @return string
     */
    public function getSubscriptionId()
    {
        return $this->subscriptionId;
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
     * Set stripeCustomer
     *
     * @param \Erp\PaymentBundle\Entity\StripeCustomer $stripeCustomer
     *
     * @return StripeRecurringPayment
     */
    public function setStripeCustomer(\Erp\PaymentBundle\Entity\StripeCustomer $stripeCustomer = null)
    {
        $this->stripeCustomer = $stripeCustomer;

        return $this;
    }

    /**
     * Get stripeCustomer
     *
     * @return \Erp\PaymentBundle\Entity\StripeCustomer
     */
    public function getStripeCustomer()
    {
        return $this->stripeCustomer;
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
}

