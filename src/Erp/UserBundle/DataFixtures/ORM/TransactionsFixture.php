<?php

namespace Erp\UserBundle\DataFixtures\ORM;

use Erp\PaymentBundle\Entity\StripeAccount;
use Erp\PaymentBundle\Entity\StripeCustomer;
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
        $transaction->setAccount($stripeAccount)
            ->setCustomer($stripeCustomer)
            ->setCurrency('usd')
            ->setAmount(ApiHelper::convertAmountToStripeFormat('100'))
            ->setBalance(ApiHelper::convertAmountToStripeFormat('100'))
            ->setPaymentMethod('card')
            ->setType(Transaction::TYPE_CHARGE)
            ->setStatus('cleared')
            ->setCreated(new \DateTime());
        $objectManager->persist($transaction);
        $objectManager->flush();

        $transaction = new Transaction();
        $transaction->setAccount($stripeAccount)
            ->setCustomer($stripeCustomer)
            ->setCurrency('usd')
            ->setAmount(ApiHelper::convertAmountToStripeFormat('150'))
            ->setBalance(ApiHelper::convertAmountToStripeFormat('250'))
            ->setPaymentMethod('card')
            ->setType(Transaction::TYPE_CHARGE)
            ->setStatus('cleared')
            ->setCreated(new \DateTime());
        $objectManager->persist($transaction);
        $objectManager->flush();

        $transaction = new Transaction();
        $transaction->setAccount($stripeAccount)
            ->setCustomer($stripeCustomer)
            ->setCurrency('usd')
            ->setAmount(ApiHelper::convertAmountToStripeFormat('-100'))
            ->setBalance(ApiHelper::convertAmountToStripeFormat('150'))
            ->setPaymentMethod('card')
            ->setType(Transaction::TYPE_CHARGE)
            ->setStatus('cleared')
            ->setCreated(new \DateTime());
        $objectManager->persist($transaction);
        $objectManager->flush();


    }

    public function getDependencies()
    {
        return array(
            ManagerFlagAssignFixture::class,
        );
    }
}