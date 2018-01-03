<?php

namespace Erp\PaymentBundle\Stripe\Manager;

use Erp\PaymentBundle\Entity\StripeCustomer;
use Erp\UserBundle\Entity\User;
use PHY\CacheBundle\Cache;
use Stripe\Customer;
use Stripe\BankAccount;
use Stripe\Card;

class UserManager
{
    /**
     * @var CustomerManager
     */
    private $customerManager;

    /**
     * @var Cache
     */
    private $cache;

    public function __construct(CustomerManager $customerManager, Cache $cache)
    {
        $this->customerManager = $customerManager;
        $this->cache = $cache;
    }

    public function getBankAccount(User $user)
    {
        /** @var StripeCustomer $stripeCustomer */
        $stripeCustomer = $user->getStripeCustomers()->last();

        if (!$stripeCustomer) {
            return;
        }

        $response = $this->customerManager->retrieve($stripeCustomer->getCustomerId());

        if (!$response->isSuccess()) {
            return;
        }
        /** @var Customer $customer */
        $customer = $response->getContent();
        $response = $this->customerManager->retrieveBankAccount($customer, $stripeCustomer->getBankAccountId());

        if (!$response->isSuccess()) {
            return;
        }
        /** @var BankAccount $bankAccount */
        $bankAccount = $response->getContent();

        return $bankAccount;
    }

    public function getCreditCard(User $user)
    {
        /** @var StripeCustomer $stripeCustomer */
        $stripeCustomer = $user->getStripeCustomers()->last();

        if (!$stripeCustomer) {
            return;
        }

        $response = $this->customerManager->retrieve($stripeCustomer->getCustomerId());

        if (!$response->isSuccess()) {
            return;
        }
        /** @var Customer $customer */
        $customer = $response->getContent();
        $response = $this->customerManager->retrieveCreditCard($customer, $stripeCustomer->getCreditCardId());

        if (!$response->isSuccess()) {
            return;
        }
        /** @var Card $creditCard */
        $creditCard = $response->getContent();

        return $creditCard;
    }
}