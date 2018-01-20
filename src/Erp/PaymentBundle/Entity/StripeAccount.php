<?php

namespace Erp\PaymentBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Erp\UserBundle\Entity\User;

/**
 * Class StripeAccount
 *
 * @ORM\Table(name="stripe_account")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class StripeAccount
{
    const DEFAULT_ACCOUNT_TYPE = 'custom';
    const DEFAULT_ACCOUNT_COUNTRY = 'US';
    const TYPE_COMPANY = 'company';
    const TYPE_INDIVIDUAL = 'individual';

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
     * @ORM\OneToOne(targetEntity="Erp\UserBundle\Entity\User", inversedBy="stripeCustomer", cascade={"persist"})
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="StripeRecurringPayment", mappedBy="account", cascade={"persist"})
     */
    private $recurringPayments;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="\Erp\StripeBundle\Entity\Invoice", mappedBy="owner", cascade={"persist"}, orphanRemoval=true)
     */
    private $invoices;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="\Erp\StripeBundle\Entity\Transaction", mappedBy="owner", cascade={"persist"}, orphanRemoval=true)
     */
    private $transactions;

    /**
     * @var string
     *
     * @ORM\Column(name="account_id", type="string")
     */
    private $accountId;

    /**
     * @var string
     *
     * @ORM\Column(name="city", type="string", nullable=true)
     */
    private $city;

    /**
     * @var string
     *
     * @ORM\Column(name="line1", type="string", nullable=true)
     */
    private $line1;

    /**
     * @var string
     *
     * @ORM\Column(name="postal_code", type="string", nullable=true)
     */
    private $postalCode;

    /**
     * @var string
     *
     * @ORM\Column(name="state", type="string", nullable=true)
     */
    private $state;

    /**
     * @var string
     *
     * @ORM\Column(name="business_name", type="string", nullable=true)
     */
    private $businessName;

    /**
     * @var string
     *
     * @ORM\Column(name="business_tax_id", type="string", nullable=true)
     */
    private $businessTaxId;

    /**
     * @var string
     *
     * @ORM\Column(name="day_of_birth", type="string", nullable=true)
     */
    private $dayOfBirth;

    /**
     * @var string
     *
     * @ORM\Column(name="month_of_birth", type="string", nullable=true)
     */
    private $monthOfBirth;

    /**
     * @var string
     *
     * @ORM\Column(name="year_of_birth", type="string", nullable=true)
     */
    private $yearOfBirth;

    /**
     * @var string
     *
     * @ORM\Column(name="first_name", type="string", nullable=true)
     */
    private $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="last_name", type="string", nullable=true)
     */
    private $lastName;

    /**
     * @var string
     *
     * @ORM\Column(name="ssn_last4", type="string", nullable=true)
     */
    private $ssnLast4;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", nullable=true)
     */
    private $type = self::TYPE_INDIVIDUAL;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="tos_acceptance_date", type="date", nullable=true)
     */
    private $tosAcceptanceDate;

    /**
     * @var string
     *
     * @ORM\Column(name="tos_acceptance_ip", type="string", nullable=true)
     */
    private $tosAcceptanceIp;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime")
     */
    private $updatedAt;

    public function __construct()
    {
        $this->invoices = new ArrayCollection();
        $this->transactions = new ArrayCollection();
        $this->recurringPayments = new ArrayCollection();
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


    public function toStripe()
    {
        return [
            'legal_entity' => [
                'address' => [
                    'city' => $this->city,
                    'line1' => $this->line1,
                    'postal_code' => $this->postalCode,
                    'state' => $this->state,
                ],
                'business_name' => $this->businessName,
                'business_tax_id' => $this->businessTaxId,
                'dob' => [
                    'day' => $this->dayOfBirth,
                    'month' => $this->monthOfBirth,
                    'year' => $this->yearOfBirth,
                ],
                'first_name' => $this->firstName,
                'last_name' => $this->lastName,
                'ssn_last_4' => $this->ssnLast4,
                'type' => $this->type,
            ],
            'tos_acceptance' => [
                'date' => $this->tosAcceptanceDate->getTimestamp(),
                'ip' => $this->tosAcceptanceIp,
            ],
        ];
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
     * Set accountId
     *
     * @param string $accountId
     *
     * @return StripeAccount
     */
    public function setAccountId($accountId)
    {
        $this->accountId = $accountId;

        return $this;
    }

    /**
     * Get accountId
     *
     * @return string
     */
    public function getAccountId()
    {
        return $this->accountId;
    }

    /**
     * Set line1
     *
     * @param string $line1
     *
     * @return StripeAccount
     */
    public function setLine1($line1)
    {
        $this->line1 = $line1;

        return $this;
    }

    /**
     * Get line1
     *
     * @return string
     */
    public function getLine1()
    {
        return $this->line1;
    }

    /**
     * Set postalCode
     *
     * @param string $postalCode
     *
     * @return StripeAccount
     */
    public function setPostalCode($postalCode)
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    /**
     * Get postalCode
     *
     * @return string
     */
    public function getPostalCode()
    {
        return $this->postalCode;
    }

    /**
     * Set state
     *
     * @param string $state
     *
     * @return StripeAccount
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state
     *
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set businessName
     *
     * @param string $businessName
     *
     * @return StripeAccount
     */
    public function setBusinessName($businessName)
    {
        $this->businessName = $businessName;

        return $this;
    }

    /**
     * Get businessName
     *
     * @return string
     */
    public function getBusinessName()
    {
        return $this->businessName;
    }

    /**
     * Set businessTaxId
     *
     * @param string $businessTaxId
     *
     * @return StripeAccount
     */
    public function setBusinessTaxId($businessTaxId)
    {
        $this->businessTaxId = $businessTaxId;

        return $this;
    }

    /**
     * Get businessTaxId
     *
     * @return string
     */
    public function getBusinessTaxId()
    {
        return $this->businessTaxId;
    }

    /**
     * Set dayOfBirth
     *
     * @param string $dayOfBirth
     *
     * @return StripeAccount
     */
    public function setDayOfBirth($dayOfBirth)
    {
        $this->dayOfBirth = $dayOfBirth;

        return $this;
    }

    /**
     * Get dayOfBirth
     *
     * @return string
     */
    public function getDayOfBirth()
    {
        return $this->dayOfBirth;
    }

    /**
     * Set monthOfBirth
     *
     * @param string $monthOfBirth
     *
     * @return StripeAccount
     */
    public function setMonthOfBirth($monthOfBirth)
    {
        $this->monthOfBirth = $monthOfBirth;

        return $this;
    }

    /**
     * Get monthOfBirth
     *
     * @return string
     */
    public function getMonthOfBirth()
    {
        return $this->monthOfBirth;
    }

    /**
     * Set yearOfBirth
     *
     * @param string $yearOfBirth
     *
     * @return StripeAccount
     */
    public function setYearOfBirth($yearOfBirth)
    {
        $this->yearOfBirth = $yearOfBirth;

        return $this;
    }

    /**
     * Get yearOfBirth
     *
     * @return string
     */
    public function getYearOfBirth()
    {
        return $this->yearOfBirth;
    }

    /**
     * Set firstName
     *
     * @param string $firstName
     *
     * @return StripeAccount
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     *
     * @return StripeAccount
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set ssnLast4
     *
     * @param string $ssnLast4
     *
     * @return StripeAccount
     */
    public function setSsnLast4($ssnLast4)
    {
        $this->ssnLast4 = $ssnLast4;

        return $this;
    }

    /**
     * Get ssnLast4
     *
     * @return string
     */
    public function getSsnLast4()
    {
        return $this->ssnLast4;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return StripeAccount
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
     * Set tosAcceptanceDate
     *
     * @param \DateTime $tosAcceptanceDate
     *
     * @return StripeAccount
     */
    public function setTosAcceptanceDate($tosAcceptanceDate)
    {
        $this->tosAcceptanceDate = $tosAcceptanceDate;

        return $this;
    }

    /**
     * Get tosAcceptanceDate
     *
     * @return \DateTime
     */
    public function getTosAcceptanceDate()
    {
        return $this->tosAcceptanceDate;
    }

    /**
     * Set tosAcceptanceIp
     *
     * @param string $tosAcceptanceIp
     *
     * @return StripeAccount
     */
    public function setTosAcceptanceIp($tosAcceptanceIp)
    {
        $this->tosAcceptanceIp = $tosAcceptanceIp;

        return $this;
    }

    /**
     * Get tosAcceptanceIp
     *
     * @return string
     */
    public function getTosAcceptanceIp()
    {
        return $this->tosAcceptanceIp;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return StripeAccount
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
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return StripeAccount
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set user
     *
     * @param \Erp\UserBundle\Entity\User $user
     *
     * @return StripeAccount
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
     * Add recurringPayment
     *
     * @param \Erp\PaymentBundle\Entity\StripeRecurringPayment $recurringPayment
     *
     * @return StripeAccount
     */
    public function addRecurringPayment(\Erp\PaymentBundle\Entity\StripeRecurringPayment $recurringPayment)
    {
        $this->recurringPayments[] = $recurringPayment;

        return $this;
    }

    /**
     * Remove recurringPayment
     *
     * @param \Erp\PaymentBundle\Entity\StripeRecurringPayment $recurringPayment
     */
    public function removeRecurringPayment(\Erp\PaymentBundle\Entity\StripeRecurringPayment $recurringPayment)
    {
        $this->recurringPayments->removeElement($recurringPayment);
    }

    /**
     * Get recurringPayments
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRecurringPayments()
    {
        return $this->recurringPayments;
    }

    /**
     * Add invoice
     *
     * @param \Erp\StripeBundle\Entity\Invoice $invoice
     *
     * @return StripeAccount
     */
    public function addInvoice(\Erp\StripeBundle\Entity\Invoice $invoice)
    {
        $this->invoices[] = $invoice;

        return $this;
    }

    /**
     * Remove invoice
     *
     * @param \Erp\StripeBundle\Entity\Invoice $invoice
     */
    public function removeInvoice(\Erp\StripeBundle\Entity\Invoice $invoice)
    {
        $this->invoices->removeElement($invoice);
    }

    /**
     * Get invoices
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getInvoices()
    {
        return $this->invoices;
    }

    /**
     * Add transaction
     *
     * @param \Erp\StripeBundle\Entity\Transaction $transaction
     *
     * @return StripeAccount
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

    /**
     * Set city
     *
     * @param string $city
     *
     * @return StripeAccount
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }
}
