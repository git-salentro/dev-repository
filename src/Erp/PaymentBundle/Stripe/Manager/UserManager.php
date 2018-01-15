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
        $stripeCustomer = $user->getStripeCustomer();

        if (!$stripeCustomer) {
            return;
        }

        $response = $this->customerManager->retrieve($stripeCustomer->getCustomerId());

        if (!$response->isSuccess()) {
            return;
        }

        /** @var Customer $content */
        $content = $response->getContent();
        $sources = $content->sources;

        foreach ($sources->data as $source) {
            if ($source instanceof BankAccount) {
                return $source;
            }
        }

        return null;
    }

    public function getCreditCard(User $user)
    {
        /** @var StripeCustomer $stripeCustomer */
        $stripeCustomer = $user->getStripeCustomer();

        if (!$stripeCustomer) {
            return;
        }

        $response = $this->customerManager->retrieve($stripeCustomer->getCustomerId());

        if (!$response->isSuccess()) {
            return;
        }

        /** @var Customer $content */
        $content = $response->getContent();
        $sources = $content->sources;

        foreach ($sources->data as $source) {
            if ($source instanceof Card) {
                return $source;
            }
        }

        return null;
    }
}