<?php

namespace Erp\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Erp\UserBundle\Entity\User;
use Erp\CoreBundle\Entity\DatesAwareInterface;
use Erp\CoreBundle\Entity\DatesAwareTrait;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class LateLateRentPayment
 *
 * @ORM\Table(name="late_rent_payment")
 * @ORM\Entity(repositoryClass="Erp\UserBundle\Repository\LateRentPaymentRepository")
 * @ORM\HasLifecycleCallbacks
 */
class LateRentPayment implements DatesAwareInterface
{
    use DatesAwareTrait;

    const RENT_PAYMENT_METADATA_KEY = 'rent_payment_id';
    const LATE_RENT_PAYMENT_TYPE = 'late_rent';
    const FEE_PAYMENT_TYPE = 'fee';

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
     * @ORM\ManyToOne(targetEntity="\Erp\UserBundle\Entity\User", inversedBy="lateRentPayments")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var integer
     *
     * @ORM\Column(name="amount", type="decimal", precision=15, scale=2)
     */
    private $amount;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string")
     */
    private $type = self::LATE_RENT_PAYMENT_TYPE;

    /**
     * @var string
     *
     * @ORM\Column(name="is_paid", type="boolean")
     */
    private $paid = false;

    /**
     * @ORM\ManyToMany(targetEntity="Erp\StripeBundle\Entity\Transaction")
     * @ORM\JoinTable(name="rent_payment_transactions",
     *      joinColumns={@ORM\JoinColumn(name="rent_payment_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="transaction_id", referencedColumnName="id", unique=true)}
     *      )
     */
    private $transactions;

    public function __construct()
    {
        $this->transactions = new ArrayCollection();
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
     * Set amount
     *
     * @param integer $amount
     *
     * @return LateRentPayment
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
     * Set type
     *
     * @param string $type
     *
     * @return LateRentPayment
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
     * Set paid
     *
     * @param boolean $paid
     *
     * @return LateRentPayment
     */
    public function setPaid($paid)
    {
        $this->paid = $paid;

        return $this;
    }

    /**
     * Get paid
     *
     * @return boolean
     */
    public function getPaid()
    {
        return $this->paid;
    }

    /**
     * Set user
     *
     * @param \Erp\UserBundle\Entity\User $user
     *
     * @return LateRentPayment
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
     * Add transaction
     *
     * @param \Erp\StripeBundle\Entity\Transaction $transaction
     *
     * @return LateRentPayment
     */
    public function addTransaction(\Erp\StripeBundle\Entity\Transaction $transaction)
    {
        $this->transactions[] = $transaction;

        return $this;
    }

    /**
     * Remove transaction
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

    public function getDayLate()
    {
        $now = new \DateTime();

        return $now->diff($this->createdAt)->format('%a');
    }
}
