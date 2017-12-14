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
     * @ORM\ManyToOne(targetEntity="Erp\UserBundle\Entity\User", inversedBy="stripeCustomers")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(name="customer_id", type="string")
     */
    private $customerId;

    /**
     * @var string
     *
     * @ORM\Column(name="credit_card_id", type="string", nullable=true)
     */
    private $creditCardId;

    /**
     * @var string
     *
     * @ORM\Column(name="bank_account_id", type="string", nullable=true)
     */
    private $bankAccountId;

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
     * @ORM\OneToMany(
     *     targetEntity="Erp\PaymentBundle\Entity\StripeRecurringPayment",
     *     mappedBy="stripeCustomer",
     *     cascade={"ALL"}
     * )
     * @ORM\OrderBy({"createdAt"="DESC"})
     */
    protected $stripeRecurringPayments;

    public function __construct()
    {
        $this->stripeRecurringPayments = new ArrayCollection();
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
     * Set creditCardId
     *
     * @param string $creditCardId
     *
     * @return StripeCustomer
     */
    public function setCreditCardId($creditCardId)
    {
        $this->creditCardId = $creditCardId;

        return $this;
    }

    /**
     * Get creditCardId
     *
     * @return string
     */
    public function getCreditCardId()
    {
        return $this->creditCardId;
    }

    /**
     * Set bankAccountId
     *
     * @param string $bankAccountId
     *
     * @return StripeCustomer
     */
    public function setBankAccountId($bankAccountId)
    {
        $this->bankAccountId = $bankAccountId;

        return $this;
    }

    /**
     * Get bankAccountId
     *
     * @return string
     */
    public function getBankAccountId()
    {
        return $this->bankAccountId;
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
     * Add stripeRecurringPayment
     *
     * @param \Erp\PaymentBundle\Entity\StripeRecurringPayment $stripeRecurringPayment
     *
     * @return StripeCustomer
     */
    public function addStripeRecurringPayment(\Erp\PaymentBundle\Entity\StripeRecurringPayment $stripeRecurringPayment)
    {
        $stripeRecurringPayment->setStripeCustomer($this);
        $this->stripeRecurringPayments[] = $stripeRecurringPayment;

        return $this;
    }

    /**
     * Remove stripeRecurringPayment
     *
     * @param \Erp\PaymentBundle\Entity\StripeRecurringPayment $stripeRecurringPayment
     */
    public function removeStripeRecurringPayment(\Erp\PaymentBundle\Entity\StripeRecurringPayment $stripeRecurringPayment)
    {
        $this->stripeRecurringPayments->removeElement($stripeRecurringPayment);
    }

    /**
     * Get stripeRecurringPayments
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getStripeRecurringPayments()
    {
        return $this->stripeRecurringPayments;
    }
}
