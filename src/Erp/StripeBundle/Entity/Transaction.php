<?php

namespace Erp\StripeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Erp\PaymentBundle\Entity\StripeAccount;
use Erp\PaymentBundle\Entity\StripeCustomer;
use  Erp\CoreBundle\Entity\CreatedAtAwareTrait;

/**
 * Class Transaction
 *
 * @ORM\Table(name="stripe_transactions")
 * @ORM\Entity(repositoryClass="Erp\StripeBundle\Repository\TransactionRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Transaction
{
    use CreatedAtAwareTrait;

    const TYPE_CHARGE = 'charge'; //stripe transaction type
    const CASH_IN = 'cash-in';
    const CASH_OUT = 'cash-out';
    const BANK_ACCOUNT_PAYMENT_METHOD = 'bank_account';
    const CREDIT_CARD_PAYMENT_METHOD = 'card';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string")
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="internal_type", type="string")
     */
    private $internalType; //fee (charge), late rent payment, rent payment


    /**
     * @var float
     *
     * @ORM\Column(name="amount", type="integer")
     */
    private $amount;

    /**
     * @var string
     *
     * @ORM\Column(name="currency", type="string")
     */
    private $currency;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @var StripeAccount
     *
     * @ORM\ManyToOne(targetEntity="\Erp\PaymentBundle\Entity\StripeAccount", inversedBy="transactions")
     * @ORM\JoinColumn(name="account_id", referencedColumnName="id", onDelete="CASCADE", nullable=true)
     */
    private $account;

    /**
     * @var StripeCustomer
     *
     * @ORM\ManyToOne(targetEntity="\Erp\PaymentBundle\Entity\StripeCustomer", inversedBy="transactions")
     * @ORM\JoinColumn(name="customer_id", referencedColumnName="id", onDelete="CASCADE", nullable=true)
     */
    private $customer;

    /**
     * @var string
     *
     * @ORM\Column(name="payment_method", type="string")
     */
    private $paymentMethod;

    /**
     * @var string
     *
     * @ORM\Column(name="payment_method_description", type="string", nullable=true)
     */
    private $paymentMethodDescription;


    /**
     * @var string
     *
     * @ORM\Column(name="metadata", type="array", nullable=true)
     */
    private $metadata;

    /**
     * @var string
     *
     * @ORM\Column(name="balance", type="string")
     */
    protected $balance;

    /**
     * @ORM\OneToOne(
     *      targetEntity="\Erp\StripeBundle\Entity\BalanceHistory",
     *      cascade={"persist"},
     *      mappedBy="transaction",
     *      orphanRemoval=true
     * )
     */
    protected $balanceHistory;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string")
     */
    protected $status;

    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        $this->createdAt = new \DateTime();
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
     * Set type
     *
     * @param string $type
     *
     * @return Transaction
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
     * Set amount
     *
     * @param integer $amount
     *
     * @return Transaction
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return integer
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set currency
     *
     * @param string $currency
     *
     * @return Transaction
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * Get currency
     *
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     *
     * @return Transaction
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set paymentMethod
     *
     * @param string $paymentMethod
     *
     * @return Transaction
     */
    public function setPaymentMethod($paymentMethod)
    {
        $this->paymentMethod = $paymentMethod;

        return $this;
    }

    /**
     * Get paymentMethod
     *
     * @return string
     */
    public function getPaymentMethod()
    {
        return $this->paymentMethod;
    }

    /**
     * Set account
     *
     * @param \Erp\PaymentBundle\Entity\StripeAccount $account
     *
     * @return Transaction
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

    /**
     * Set customer
     *
     * @param \Erp\PaymentBundle\Entity\StripeCustomer $customer
     *
     * @return Transaction
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
     * Set metadata
     *
     * @param array $metadata
     *
     * @return Transaction
     */
    public function setMetadata($metadata)
    {
        $this->metadata = $metadata;

        return $this;
    }

    /**
     * Get metadata
     *
     * @return array
     */
    public function getMetadata()
    {
        return $this->metadata;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     *
     * @return Transaction
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @param string $balance
     * @return Transaction
     */
    public function setBalance($balance)
    {
        $this->balance = $balance;
        return $this;
    }

    /**
     * @return string
     */
    public function getBalance()
    {
        return $this->balance;
    }

    /**
     * @return mixed
     */
    public function getBalanceHistory()
    {
        return $this->balanceHistory;
    }

    /**
     * @param mixed $balanceHistory
     */
    public function setBalanceHistory($balanceHistory)
    {
        $this->balanceHistory = $balanceHistory;
    }

    /**
     * @return string
     */
    public function getInternalType()
    {
        return $this->internalType;
    }

    /**
     * @param string $internalType
     */
    public function setInternalType($internalType)
    {
        $this->internalType = $internalType;
    }

    /**
     * @return string
     */
    public function getPaymentMethodDescription()
    {
        return $this->paymentMethodDescription;
    }

    /**
     * @param string $paymentMethodDescription
     */
    public function setPaymentMethodDescription($paymentMethodDescription)
    {
        $this->paymentMethodDescription = $paymentMethodDescription;
    }
}
