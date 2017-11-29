<?php

namespace Erp\PaymentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Erp\UserBundle\Entity\User;

/**
 * Class UnitSettings
 *
 * @ORM\Table(name="user_unit_settings")
 * @ORM\Entity
 */
class UserUnitSettings
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
     * @ORM\ManyToOne(targetEntity="Erp\UserBundle\Entity\User", inversedBy="stripeCustomers")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $user;

    /**
     * @var integer
     *
     * @ORM\Column(name="initial_quantity", type="integer")
     */
    private $initialQuantity;

    /**
     * @var integer
     *
     * @ORM\Column(name="quantity_per_unit", type="integer")
     */
    private $quantityPerUnit;

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
     * Set initialQuantity
     *
     * @param integer $initialQuantity
     *
     * @return UserUnitSettings
     */
    public function setInitialQuantity($initialQuantity)
    {
        $this->initialQuantity = $initialQuantity;

        return $this;
    }

    /**
     * Get initialQuantity
     *
     * @return integer
     */
    public function getInitialQuantity()
    {
        return $this->initialQuantity;
    }

    /**
     * Set quantityPerUnit
     *
     * @param integer $quantityPerUnit
     *
     * @return UserUnitSettings
     */
    public function setQuantityPerUnit($quantityPerUnit)
    {
        $this->quantityPerUnit = $quantityPerUnit;

        return $this;
    }

    /**
     * Get quantityPerUnit
     *
     * @return integer
     */
    public function getQuantityPerUnit()
    {
        return $this->quantityPerUnit;
    }

    /**
     * Set user
     *
     * @param \Erp\UserBundle\Entity\User $user
     *
     * @return UserUnitSettings
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
}
