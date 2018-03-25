<?php

namespace Erp\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Erp\UserBundle\Entity\User;
use Erp\CoreBundle\Entity\DatesAwareInterface;
use Erp\CoreBundle\Entity\DatesAwareTrait;

/**
 * Class RentPaymentBalanceBalance
 *
 * @ORM\Table(name="rent_payment_balance")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class RentPaymentBalance implements DatesAwareInterface
{
    use DatesAwareTrait;

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
     * @ORM\OneToOne(targetEntity="\Erp\UserBundle\Entity\User", inversedBy="rentPaymentBalance")
     */
    protected $user;

    /**
     * @var integer
     *
     * @ORM\Column(name="balance", type="integer")
     */
    private $balance = 0;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="debt_start_at", type="date", nullable=true)
     */
    private $debtStartAt;

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
        if ($this->balance < 0 && $this->debtStartAt === null) {
            $this->debtStartAt = new \DateTime();

            return;
        }

        if ($this->balance >= 0) {
            $this->debtStartAt = null;
        }

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
     * Set balance
     *
     * @param integer $balance
     *
     * @return RentPaymentBalance
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
     * Set user
     *
     * @param \Erp\UserBundle\Entity\User $user
     *
     * @return RentPaymentBalance
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
    
    public function takeMoneyFromBalance($amount)
    {
        $this->balance -= $amount;
    }

    public function depositMoneyToBalance($amount)
    {
        $this->balance += $amount;
    }

    public function getDayLate()
    {
        if (!$this->debtStartAt) {
            return;
        }

        $now = new \DateTime();
        $createdAt = \DateTimeImmutable::createFromMutable($this->debtStartAt)->modify('-1 day');

        $createdAt->setTime(0, 0);
        $now->setTime(0, 0);

        return $now->diff($createdAt)->format('%a');
    }
}
