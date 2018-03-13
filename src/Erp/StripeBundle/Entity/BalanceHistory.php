<?php

namespace Erp\StripeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Stripe\Balance;

/**
 * BalanceHistory
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Erp\StripeBundle\Entity\BalanceHistoryRepository")
 */
class BalanceHistory
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
     * @var Transaction $transaction
     * @ORM\OneToOne(
     *      targetEntity="\Erp\StripeBundle\Entity\Transaction",
     *      inversedBy="balanceHistory"
     * )
     */
    protected $transaction;


    /**
     * @var string
     *
     * @ORM\Column(name="amount", type="string", length=255)
     */
    private $amount;




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
     * @param string $amount
     *
     * @return BalanceHistory
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return string
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Get document
     *
     * @return Transaction
     */
    public function getTransaction()
    {
        return $this->transaction;
    }

    /**
     * Set transaction
     *
     * @param Transaction $document
     *
     * @return BalanceHistory
     */
    public function setTransaction(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }
}

