<?php

namespace Erp\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="late_rent_payment_settings")
 * @ORM\Entity
 */
class LateRentPaymentSettings
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
     * @ORM\OneToOne(targetEntity="\Erp\UserBundle\Entity\User", inversedBy="lateRentPaymentSettings")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var integer
     *
     * @ORM\Column(name="fee", type="integer", nullable=true)
     */
    private $fee;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_allow_rent_payment", type="boolean", nullable=true)
     */
    private $allowRentPayment;

    /**
     * @var boolean
     * TODO Create separate table?
     * @ORM\Column(name="category", type="string", nullable=true)
     */
    private $category;

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
     * Set fee
     *
     * @param integer $fee
     *
     * @return LateRentPaymentSettings
     */
    public function setFee($fee)
    {
        $this->fee = $fee;

        return $this;
    }

    /**
     * Get fee
     *
     * @return integer
     */
    public function getFee()
    {
        return $this->fee;
    }

    /**
     * Set allowRentPayment
     *
     * @param boolean $allowRentPayment
     *
     * @return LateRentPaymentSettings
     */
    public function setAllowRentPayment($allowRentPayment)
    {
        $this->allowRentPayment = (boolean) $allowRentPayment;

        return $this;
    }

    /**
     * Get allowRentPayment
     *
     * @return boolean
     */
    public function getAllowRentPayment()
    {
        return $this->allowRentPayment;
    }

    /**
     * Set category
     *
     * @param string $category
     *
     * @return LateRentPaymentSettings
     */
    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return string
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set user
     *
     * @param \Erp\UserBundle\Entity\User $user
     *
     * @return LateRentPaymentSettings
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

    public function isAllowRentPayment()
    {
        return $this->allowRentPayment;
    }
}
