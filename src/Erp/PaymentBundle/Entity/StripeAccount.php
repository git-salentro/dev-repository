<?php

namespace Erp\PaymentBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Erp\UserBundle\Entity\User;

/**
 * Class StripeAccount
 *
 * @ORM\Table(name="stripe_account")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class StripeAccount
{
    const DEFAULT_ACCOUNT_TYPE = 'custom';
    const DEFAULT_ACCOUNT_COUNTRY = 'US';

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
     * @ORM\OneToOne(targetEntity="Erp\UserBundle\Entity\User", inversedBy="stripeCustomer", cascade={"persist"})
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="StripeRecurringPayment", mappedBy="account", cascade={"persist"})
     */
    private $recurringPayments;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="\Erp\StripeBundle\Entity\Invoice", mappedBy="owner", cascade={"persist"}, orphanRemoval=true)
     */
    private $invoices;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="\Erp\StripeBundle\Entity\Transaction", mappedBy="owner", cascade={"persist"}, orphanRemoval=true)
     */
    private $transactions;

    /**
     * @var string
     *
     * @ORM\Column(name="account_id", type="string")
     */
    private $accountId;

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

    public function __construct()
    {
        $this->invoices = new ArrayCollection();
        $this->transactions = new ArrayCollection();
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
     * Set accountId
     *
     * @param string $accountId
     *
     * @return StripeAccount
     */
    public function setAccountId($accountId)
    {
        $this->accountId = $accountId;

        return $this;
    }

    /**
     * Get accountId
     *
     * @return string
     */
    public function getAccountId()
    {
        return $this->accountId;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return StripeAccount
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
     * @return StripeAccount
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
     * @return StripeAccount
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
     * Add invoice
     *
     * @param \Erp\StripeBundle\Entity\Invoice $invoice
     *
     * @return StripeAccount
     */
    public function addInvoice(\Erp\StripeBundle\Entity\Invoice $invoice)
    {
        $this->invoices[] = $invoice;

        return $this;
    }

    /**
     * Remove invoice
     *
     * @param \Erp\StripeBundle\Entity\Invoice $invoice
     */
    public function removeInvoice(\Erp\StripeBundle\Entity\Invoice $invoice)
    {
        $this->invoices->removeElement($invoice);
    }

    /**
     * Get invoices
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getInvoices()
    {
        return $this->invoices;
    }

    /**
     * Add transaction
     *
     * @param \Erp\StripeBundle\Entity\Invoice $invoice
     *
     * @return StripeAccount
     */
    public function addTransaction(\Erp\StripeBundle\Entity\Transaction $transaction)
    {
        $this->transactions[] = $transaction;

        return $this;
    }

    /**
     * Remove invoice
     *
     * @param \Erp\StripeBundle\Entity\Transaction $transaction
     */
    public function removeTransaction(\Erp\StripeBundle\Entity\Transaction $transaction)
    {
        $this->transactions->removeElement($transaction);
    }

    /**
     * Get transactions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTransactions()
    {
        return $this->transactions;
    }
}
