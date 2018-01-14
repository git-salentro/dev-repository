<?php

namespace Erp\StripeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Erp\PaymentBundle\Entity\StripeAccount;

/**
 * Class Transaction
 *
 * @ORM\Table(name="stripe_transactions")
 * @ORM\Entity(repositoryClass="Erp\StripeBundle\Repository\TransactionRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Transaction
{
    const TYPE_CHARGE = 'charge';

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
     * @var float
     *
     * @ORM\Column(name="amount", type="float", scale=2)
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
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id", onDelete="CASCADE", nullable=true)
     */
    private $owner;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

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
     * @param float $amount
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
     * @return float
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Transaction
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
     * Set owner
     *
     * @param \Erp\PaymentBundle\Entity\StripeAccount $owner
     *
     * @return Transaction
     */
    public function setOwner(\Erp\PaymentBundle\Entity\StripeAccount $owner = null)
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * Get owner
     *
     * @return \Erp\PaymentBundle\Entity\StripeAccount
     */
    public function getOwner()
    {
        return $this->owner;
    }
}
