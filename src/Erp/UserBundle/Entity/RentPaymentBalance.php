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
 * @ORM\HasLifecycleCallbacks()
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
     * @ORM\ManyToOne(targetEntity="\Erp\UserBundle\Entity\User", inversedBy="RentPaymentBalances")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var integer
     *
     * @ORM\Column(name="balance", type="integer")
     */
    private $balance = 0;

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
}
