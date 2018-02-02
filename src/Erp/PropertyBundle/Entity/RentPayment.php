<?php

namespace Erp\PropertyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Erp\UserBundle\Entity\User;

/**
 * Class LateRentPayment
 *
 * @ORM\Table(name="rent_payment")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class RentPayment
{
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
     * @ORM\OneToOne(targetEntity="\Erp\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_payment_at", type="date")
     */
    private $lastPaymentAt;

    /**
     * @var integer
     *
     * @ORM\Column(name="balance", type="integer")
     */
    private $balance;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="debt_start_at", type="date", nullable=true)
     */
    private $debtStartAt;

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function checkBalance()
    {
        if ($this->balance < 0 && $this->debtStartAt === null) {
            $this->debtStartAt = new \DateTime();
        }

        if ($this->balance >= 0) {
            $this->debtStartAt = null;
        }
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
     * Set lastPaymentAt
     *
     * @param \DateTime $lastPaymentAt
     *
     * @return RentPayment
     */
    public function setLastPaymentAt($lastPaymentAt)
    {
        $this->lastPaymentAt = $lastPaymentAt;

        return $this;
    }

    /**
     * Get lastPaymentAt
     *
     * @return \DateTime
     */
    public function getLastPaymentAt()
    {
        return $this->lastPaymentAt;
    }

    /**
     * Set user
     *
     * @param \Erp\UserBundle\Entity\User $user
     *
     * @return RentPayment
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
     * Set balance
     *
     * @param integer $balance
     *
     * @return RentPayment
     */
    public function setBalance($balance)
    {
        $this->balance = $balance;

        return $this;
    }

    /**
     * Get balance
     *
     * @return integer
     */
    public function getBalance()
    {
        return $this->balance;
    }

    /**
     * Set debtStartAt
     *
     * @param \DateTime $debtStartAt
     *
     * @return RentPayment
     */
    public function setDebtStartAt($debtStartAt)
    {
        $this->debtStartAt = $debtStartAt;

        return $this;
    }

    /**
     * Get debtStartAt
     *
     * @return \DateTime
     */
    public function getDebtStartAt()
    {
        return $this->debtStartAt;
    }

    public function takeMoneyFromBalance($amount)
    {
        $this->balance -= $amount;
    }

    public function depositMoneyToBalance($amount)
    {
        $this->balance += $amount;
    }

    public function getNumberOfDayLate()
    {
        if (!$this->debtStartAt) {
            return;
        }

        return (new \DateTime())->diff($this->debtStartAt)->format('%a');
    }
}
