<?php

namespace Erp\UserBundle\DataFixtures\ORM;

use Erp\PaymentBundle\Entity\StripeAccount;
use Erp\PaymentBundle\Entity\StripeCustomer;
use Erp\StripeBundle\Entity\BalanceHistory;
use Erp\StripeBundle\Entity\Transaction;
use Erp\StripeBundle\Helper\ApiHelper;
use Erp\UserBundle\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class TransactionsFixture extends Fixture
{
    /**
     * @inheritdoc
     */
    public function load(ObjectManager $objectManager)
    {
        /** @var User $manager */
        $manager = $this->getReference('tonystark@test.com');
        /** @var User $landlord */
        $landlord = $this->getReference('johndoe@test.com');

        $stripeAccount = new StripeAccount();
        $stripeAccount->setUser($manager)
            ->setAccountId('0987654321')//mock data
            ->setFirstName($manager->getFirstName())
            ->setLastName($manager->getLastName());
        $objectManager->persist($stripeAccount);
        $objectManager->flush();

        $stripeCustomer = new StripeCustomer();
        $stripeCustomer->setUser($landlord)
            ->setCustomerId('1234567890')//mock data
        ;
        $objectManager->persist($stripeCustomer);
        $objectManager->flush();

        $transaction = new Transaction();
        $amount = ApiHelper::convertAmountToStripeFormat('100');
        $balance = ApiHelper::convertAmountToStripeFormat('100');

        $transaction->setAccount($stripeAccount)
            ->setCustomer($stripeCustomer)
            ->setCurrency('usd')
            ->setStatus('cleared')
            ->setCreated(new \DateTime())
            ->setAmount($amount)
            ->setBalance($balance)
            ->setPaymentMethod(Transaction::CREDIT_CARD_PAYMENT_METHOD)
            ->setType(Transaction::TYPE_CHARGE)
            ->setInternalType('late_rent_payment');
        $objectManager->persist($transaction);
        $objectManager->flush();

        $balanceHistory = new BalanceHistory();
        $balanceHistory->setTransaction($transaction);
        $objectManager->persist($balanceHistory);
        $balanceHistory
            ->setAmount($amount)
            ->setBalance($balance)
        ;
        $objectManager->persist($balanceHistory);
        $objectManager->flush();


        $transaction = new Transaction();
        $amount = ApiHelper::convertAmountToStripeFormat('150');
        $balance = ApiHelper::convertAmountToStripeFormat('250');
        $transaction->setAccount($stripeAccount)
            ->setCustomer($stripeCustomer)
            ->setCurrency('usd')
            ->setStatus('cleared')
            ->setCreated(new \DateTime())
            ->setAmount($amount)
            ->setBalance($balance)
            ->setPaymentMethod(Transaction::CREDIT_CARD_PAYMENT_METHOD)
            ->setType(Transaction::TYPE_CHARGE)
            ->setInternalType('rent_payment');
        $objectManager->persist($transaction);
        $objectManager->flush();

        $balanceHistory = new BalanceHistory();
        $balanceHistory->setTransaction($transaction);
        $objectManager->persist($balanceHistory);
        $balanceHistory
            ->setAmount($amount)
            ->setBalance($balance)
        ;
        $objectManager->persist($balanceHistory);
        $objectManager->flush();

        $transaction = new Transaction();
        $amount = ApiHelper::convertAmountToStripeFormat('-100');
        $balance = ApiHelper::convertAmountToStripeFormat('150');
        $transaction->setAccount($stripeAccount)
            ->setCustomer($stripeCustomer)
            ->setType(Transaction::TYPE_CHARGE)
            ->setCurrency('usd')
            ->setStatus('cleared')
            ->setCreated(new \DateTime())
            ->setAmount($amount)
            ->setBalance($balance)
            ->setPaymentMethod(Transaction::CREDIT_CARD_PAYMENT_METHOD)
            ->setInternalType('fee');
        $objectManager->persist($transaction);
        $objectManager->flush();

        $balanceHistory = new BalanceHistory();
        $balanceHistory->setTransaction($transaction);
        $objectManager->persist($balanceHistory);
        $balanceHistory
            ->setAmount($amount)
            ->setBalance($balance)
        ;
        $objectManager->persist($balanceHistory);
        $objectManager->flush();


    }

    public function getDependencies()
    {
        return array(
            ManagerFlagAssignFixture::class,
        );
    }
}