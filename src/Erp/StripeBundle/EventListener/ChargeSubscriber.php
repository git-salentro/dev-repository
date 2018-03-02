<?php

namespace Erp\StripeBundle\EventListener;

use Erp\StripeBundle\Entity\Transaction;
use Erp\StripeBundle\Event\ChargeEvent;
use Erp\PaymentBundle\Entity\StripeAccount;
use Erp\PaymentBundle\Entity\StripeCustomer;
use Stripe\Charge;

class ChargeSubscriber extends AbstractSubscriber
{
    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return [
            ChargeEvent::SUCCEEDED => 'onChargeSucceeded',
        ];
    }

    public function onChargeSucceeded(ChargeEvent $event)
    {
        $stripeEvent = $event->getStripeEvent();
        $stripeCharge = $stripeEvent->data->object;

        /** @var Charge $stripeCharge */
        if (!$stripeCharge instanceof Charge) {
            throw new \InvalidArgumentException('ChargeSubscriber::onChargeSucceeded() accepts only Stripe\Charge objects as second parameter.');
        }

        if (!$stripeCharge->customer) {
            return;
        }
        //TODO: ERP-138
        //TODO: request from https://stripe.com/docs/api#balance_transaction_retrieve by "balance_transaction"
        // $balanceStripeId = $stripeCharge->balance_transaction;
        //TODO: get from response Balance and Status
        //Example request: \Stripe\Stripe::setApiKey("sk_test_BQokikJOvBiI2HlWgH4olfQ2");
        //Example request: \Stripe\BalanceTransaction::retrieve("txn_19XJJ02eZvKYlo2ClwuJ1rbA");
        //$stripeTransactionBalance = ...
        //$stripeTransactionStatus =  ...

        $transaction = new Transaction();

        $transaction->setAmount($stripeCharge->amount)
            //TODO: ERP-138
            //            ->setBalance($stripeTransactionBalance)
            //            ->setStatus($stripeTransactionStatus)
            ->setCurrency($stripeCharge->currency)
            ->setCreated((new \DateTime())->setTimestamp($stripeCharge->created))
            ->setType(Transaction::TYPE_CHARGE)
            ->setPaymentMethod($stripeCharge->source->object);

        if (isset($stripeEvent->account)) {
            $account = $this->getAccount($stripeEvent->account);
            $transaction->setAccount($account);
        }

        $customer = $this->getCustomer($stripeCharge->customer);
        $transaction->setCustomer($customer);

        $em = $this->registry->getManagerForClass(Transaction::class);

        $em->persist($transaction);
        $em->flush();
    }

    private function getAccount($accountId)
    {
        $em = $this->registry->getManagerForClass(StripeAccount::class);

        return $em->getRepository(StripeAccount::class)->findOneBy(['accountId' =>$accountId]);
    }

    private function getCustomer($customerId)
    {
        $em = $this->registry->getManagerForClass(StripeCustomer::class);

        return $em->getRepository(StripeCustomer::class)->findOneBy(['customerId' =>$customerId]);
    }
}
