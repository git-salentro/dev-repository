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


        $em = $this->registry->getManagerForClass(Transaction::class);

        //        $repository = $em->getRepository(Transaction::class);
        //        $transaction= $repository->findOneBy(['account'=>$stripeEvent->account]);

        $transaction = new Transaction();

        $transaction->setAmount($stripeCharge->amount)
            ->setCurrency($stripeCharge->currency)
            ->setCreated((new \DateTime())->setTimestamp($stripeCharge->created))
            ->setType(Transaction::TYPE_CHARGE)
            ->setPaymentMethod($stripeCharge->source->object)
            ->setPaymentMethodDescription($stripeCharge->source->brand);

        if (isset($stripeEvent->account)) {
            $account = $this->getAccount($stripeEvent->account);
            $transaction->setAccount($account);
        }

        $customer = $this->getCustomer($stripeCharge->customer);
        $transaction->setCustomer($customer);


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
