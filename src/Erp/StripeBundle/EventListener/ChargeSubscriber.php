<?php

namespace Erp\StripeBundle\EventListener;

use Erp\StripeBundle\Entity\BalanceHistory;
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

        $em = $this->registry->getManagerForClass(Transaction::class);
        $repository = $em->getRepository(Transaction::class);


        $stripeAccount = $this->getAccount($stripeCharge->metadata->account);
        $stripeAccountId = ($stripeAccount instanceof StripeAccount) ? $stripeAccount->getId() : null;
        $stripeCustomer = $this->getCustomer($stripeCharge->customer);
        $internalType = $stripeCharge->metadata->internalType;

        //get current balance based on
        /* @var $previousTransaction Transaction */

        $previousTransaction = $repository->findOneBy(['account' => $stripeAccountId], ['created' => 'DESC']);

        if ($previousTransaction instanceof Transaction && isset($stripeCharge->amount)) {
            $previousBalance = $previousTransaction->getBalanceHistory()->getBalance();
            $balance = $stripeCharge->amount + $previousBalance;
        } else {
            //first balance
            $balance = $stripeCharge->amount;
        }
        $transaction = $repository->findOneBy(['account' => $stripeAccountId, 'amount' => $stripeCharge->amount,'created' => (new \DateTime())->setTimestamp($stripeCharge->created)]);

        if ($transaction instanceof Transaction) {
            //exist transaction
            $balanceHistory = $transaction->getBalanceHistory();
        } else {

            //new transaction
            $transaction = new Transaction();
            $transaction->setBalance($balance)
                ->setType(Transaction::TYPE_CHARGE)
                ->setCurrency($stripeCharge->currency)
                ->setCreated((new \DateTime())->setTimestamp($stripeCharge->created))
                ->setAmount($stripeCharge->amount)
                ->setPaymentMethod($stripeCharge->source->object)
                ->setPaymentMethodDescription($stripeCharge->source->brand);
            $transaction->setInternalType($internalType);
            $balanceHistory = new BalanceHistory();
            $balanceHistory->setTransaction($transaction);
        }

        //update for all cases
        $transaction->setStatus($stripeCharge->status);
        $transaction->setAccount($stripeAccount);
        $transaction->setCustomer($stripeCustomer);
        $em->persist($transaction);
        $em->flush();

        $balanceHistory->setAmount($stripeCharge->amount);
        $balanceHistory->setBalance($balance);
        $em->persist($balanceHistory);
        $em->flush();


    }

    private function getAccount($accountId)
    {
        $em = $this->registry->getManagerForClass(StripeAccount::class);

        return $em->getRepository(StripeAccount::class)->findOneBy(['accountId' => $accountId]);
    }

    private function getCustomer($customerId)
    {
        $em = $this->registry->getManagerForClass(StripeCustomer::class);

        return $em->getRepository(StripeCustomer::class)->findOneBy(['customerId' => $customerId]);
    }
}
