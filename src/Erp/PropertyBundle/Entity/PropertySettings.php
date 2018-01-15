<?php

namespace Erp\PropertyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class PropertySetting
 *
 * @ORM\Table(name="properties_settings")
 * @ORM\Entity
 */
class PropertySettings
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
     * @var Property
     *
     * @ORM\OneToOne(targetEntity="Property")
     * @ORM\JoinColumn(name="property_id", referencedColumnName="id")
     */
    private $property;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="day_until_due", type="integer")
     */
    private $dayUntilDue = 1;

    /**
     * @var float
     *
     * @ORM\Column(name="payment_amount", type="float", )
     */
    private $paymentAmount;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_allow_partial_payments", type="boolean")
     */
    private $allowPartialPayments = true;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_allow_credit_card_payments", type="boolean")
     */
    private $allowCreditCardPayments = true;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_allow_auto_draft", type="boolean")
     */
    private $allowAutoDraft = false;

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
     * Set dayUntilDue
     *
     * @param integer $dayUntilDue
     *
     * @return PropertySettings
     */
    public function setDayUntilDue($dayUntilDue)
    {
        $this->dayUntilDue = $dayUntilDue;

        return $this;
    }

    /**
     * Get dayUntilDue
     *
     * @return integer
     */
    public function getDayUntilDue()
    {
        return $this->dayUntilDue;
    }

    /**
     * Set paymentAmount
     *
     * @param float $paymentAmount
     *
     * @return PropertySettings
     */
    public function setPaymentAmount($paymentAmount)
    {
        $this->paymentAmount = $paymentAmount;

        return $this;
    }

    /**
     * Get paymentAmount
     *
     * @return float
     */
    public function getPaymentAmount()
    {
        return $this->paymentAmount;
    }

    /**
     * Set allowPartialPayments
     *
     * @param boolean $allowPartialPayments
     *
     * @return PropertySettings
     */
    public function setAllowPartialPayments($allowPartialPayments)
    {
        $this->allowPartialPayments = $allowPartialPayments;

        return $this;
    }

    /**
     * Get allowPartialPayments
     *
     * @return boolean
     */
    public function getAllowPartialPayments()
    {
        return $this->allowPartialPayments;
    }

    /**
     * Set allowCreditCardPayments
     *
     * @param boolean $allowCreditCardPayments
     *
     * @return PropertySettings
     */
    public function setAllowCreditCardPayments($allowCreditCardPayments)
    {
        $this->allowCreditCardPayments = $allowCreditCardPayments;

        return $this;
    }

    /**
     * Get allowCreditCardPayments
     *
     * @return boolean
     */
    public function getAllowCreditCardPayments()
    {
        return $this->allowCreditCardPayments;
    }

    /**
     * Set allowAutoDraft
     *
     * @param boolean $allowAutoDraft
     *
     * @return PropertySettings
     */
    public function setAllowAutoDraft($allowAutoDraft)
    {
        $this->allowAutoDraft = $allowAutoDraft;

        return $this;
    }

    /**
     * Get allowAutoDraft
     *
     * @return boolean
     */
    public function getAllowAutoDraft()
    {
        return $this->allowAutoDraft;
    }

    /**
     * Set property
     *
     * @param \Erp\PropertyBundle\Entity\Property $property
     *
     * @return PropertySettings
     */
    public function setProperty(\Erp\PropertyBundle\Entity\Property $property = null)
    {
        $this->property = $property;

        return $this;
    }

    /**
     * Get property
     *
     * @return \Erp\PropertyBundle\Entity\Property
     */
    public function getProperty()
    {
        return $this->property;
    }

    public function replace(PropertySettings $settings)
    {
       $this->dayUntilDue = $settings->getDayUntilDue();
       $this->paymentAmount = $settings->getPaymentAmount();
       $this->allowPartialPayments = $settings->getAllowPartialPayments();
       $this->allowCreditCardPayments = $settings->getAllowCreditCardPayments();
       $this->allowAutoDraft = $settings->getAllowAutoDraft();
    }
}
