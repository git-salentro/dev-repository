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

    const LATE_RENT_PAYMENT_TYPE = 'late_rent';
    const FEE_PAYMENT_TYPE = 'fee';
    const LATE_RENT_PAYMENT_TYPE_LABELS = [
        self::LATE_RENT_PAYMENT_TYPE => 'Late Rent',
        self::FEE_PAYMENT_TYPE => 'Fee',
    ];

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

    public function getTypeLabel()
    {
        return self::LATE_RENT_PAYMENT_TYPE_LABELS[$this->type];
    }
}
